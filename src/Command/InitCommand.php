<?php

namespace App\Command;

use App\Entity\Configuration;
use App\Enum\ModeEnum;
use App\Immutable\SystemConfig;
use App\Service\PrimaryTaskService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'uss:initialize',
    description: 'Initialize User Synthetics Application',
    hidden: false,
)]
class InitCommand extends Command
{
    const ENV_PROD = 'prod';
    const ENV_DEV = 'dev';

    protected InputInterface $input;
    protected OutputInterface $output;
    protected SymfonyStyle $symfonyStyle;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected PrimaryTaskService $primaryTaskService,
        protected KernelInterface $kernel
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $this->symfonyStyle = new SymfonyStyle($this->input, $this->output);

        try {

            if($this->kernel->getEnvironment() === self::ENV_DEV) {
                $this->symfonyStyle->warning('You are currently in "development" environment');
            }

            $this->updateComposerPackages();
            $this->overloadAdminConfiguration();
            $this->computeAssetMapperResource(); 
            $this->generateSecretKey();

        } catch(Exception $exception) {

            $this->symfonyStyle->error(
                sprintf(
                    "%s on %s:%s", 
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine(),
                )
            );

            return Command::FAILURE;

        }

        $this->symfonyStyle->success('User Synthetics Initialization Completed');

        return Command::SUCCESS;
    }

    protected function updateComposerPackages(): void
    {
        $this->symfonyStyle->title("Revising Composer Packages");

        $this->runSymfonyConsoleCommand(['composer', 'update']);

        $this->symfonyStyle->success("Composer Packages Revised");
    }

    protected function overloadAdminConfiguration(): void
    {
        $configurationRepository = $this->entityManager->getRepository(Configuration::class);
        
        $this->symfonyStyle->title('Updating admin configurations');
        
        foreach(SystemConfig::getConfigurationStructure() as $key => $context) {
            
            $config = $configurationRepository->findOneBy(['metaKey' => $key]);

            if(!$config) {
                    
                $config = (new Configuration())
                    ->setMetaKey($key)
                    ->setMetaValue($context['value'])
                    ->setBitwiseMode($context['mode'])
                ;

                $this->entityManager->persist($config);

                $this->symfonyStyle->text(
                    sprintf(
                        '[<info>%s</info>] = %s', 
                        $key, 
                        implode(' ⏎ ', array_map('trim', explode("\n", $config->getMetaValueAsString())))
                    ),
                );
            }
        }

        $this->entityManager->flush();

        $this->symfonyStyle->success('Admin configuration updated');
    }

    protected function computeAssetMapperResource(): void
    {
        $this->symfonyStyle->title("Initializing Asset Mapper");
        
        $this->runSymfonyConsoleCommand(['php', 'bin/console', 'importmap:install']);

        # If env == prod
        if($this->kernel->getEnvironment() === self::ENV_PROD) {
            
            $this->runSymfonyConsoleCommand(['php', 'bin/console', 'asset-map:compile']);

        };
        
        $this->symfonyStyle->success('Asset Mapper Initialized');
    }

    protected function generateSecretKey(): void
    {
        $this->symfonyStyle->title('Generating APP_SECRET');

        if($this->kernel->getEnvironment() === self::ENV_PROD) {

            $secret = $this->primaryTaskService->keygen(32);
            $result = shell_exec('sed -i -E "s/^APP_SECRET=.{32}$/APP_SECRET=' . $secret . '/" .env');

            $this->symfonyStyle->success('New APP_SECRET was generated: ' . $secret);

            return;
        }

        $this->symfonyStyle->warning('APP_SECRET not generated! Skipped in "dev" environment');
    }

    private function runSymfonyConsoleCommand(array $command): void
    {
        $process = new Process($command);
        $process->run();

        if(!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->symfonyStyle->text($process->getOutput());
    }
}