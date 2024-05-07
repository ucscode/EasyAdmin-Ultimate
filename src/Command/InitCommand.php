<?php

namespace App\Command;

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
use Ucscode\KeyGenerator\KeyGenerator;

#[AsCommand(
    name: 'uss:initialize',
    description: 'Initialize User Synthetics Application',
    hidden: false,
)]
class InitCommand extends Command
{
    public const ENV_PROD = 'prod';
    public const ENV_DEV = 'dev';

    protected InputInterface $input;
    protected OutputInterface $output;
    protected SymfonyStyle $symfonyStyle;

    protected KeyGenerator $keyGenerator;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected KernelInterface $kernel
    ) {
        parent::__construct();
        $this->keyGenerator = new KeyGenerator();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $this->symfonyStyle = new SymfonyStyle($this->input, $this->output);

        try {

            if(!$this->isProductionEnvironment()) {
                $this->symfonyStyle->warning('You are currently in "development" environment');
            }

            $this->updateComposerPackages();
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
        $this->symfonyStyle->title("Updating Composer Packages");

        $this->runSymfonyConsoleCommand(['composer', 'update']);

        $this->symfonyStyle->success("Composer Packages Revised");
    }

    protected function computeAssetMapperResource(): void
    {
        $this->symfonyStyle->title("Initializing Asset Mapper");

        $this->runSymfonyConsoleCommand(['php', 'bin/console', 'importmap:install']);

        # If env == prod
        if($this->isProductionEnvironment()) {

            $this->runSymfonyConsoleCommand(['php', 'bin/console', 'asset-map:compile']);

        };

        $this->symfonyStyle->success('Asset Mapper Initialized');
    }

    protected function generateSecretKey(): void
    {
        $this->symfonyStyle->title('Generating APP_SECRET');

        if($this->isProductionEnvironment()) {

            $secretKey = $this->keyGenerator->generateKey(32);
            $result = shell_exec('sed -i -E "s/^APP_SECRET=.{32}$/APP_SECRET=' . $secretKey . '/" .env');

            $this->symfonyStyle->success('New APP_SECRET was generated: ' . $secretKey);

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

    private function isProductionEnvironment(): bool
    {
        return $this->kernel->getEnvironment() === self::ENV_PROD;
    }
}
