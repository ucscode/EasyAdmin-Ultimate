<?php

namespace App\Command;

use App\Configuration\UserPropertyPattern;
use App\Entity\User\Property;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
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
    name: 'eau:initialize',
    description: 'Initialize User Synthetics Application',
    hidden: false,
)]
class EauInitializeCommand extends Command
{
    public const ENV_PROD = 'prod';
    public const ENV_DEV = 'dev';

    protected InputInterface $input;
    protected OutputInterface $output;
    protected SymfonyStyle $symfonyStyle;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected KernelInterface $kernel
    ) {
        parent::__construct();
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

            // $this->updateComposerPackages();
            $this->computeAssetMapperResource();
            $this->updateUserProperties();

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

    protected function updateUserProperties(): void
    {
        $this->symfonyStyle->title('Updating User Properties');

        $userRepository = $this->entityManager->getRepository(User::class);

        foreach((new UserPropertyPattern())->getPatterns() as $metaKey => $pattern) {

            $query = $userRepository->createQueryBuilder('U')
                ->leftJoin(Property::class, 'P', Join::WITH, 'U = P.user')
                ->groupBy('U.id')
                ->having('MAX(CASE WHEN P.metaKey = :metaKey THEN 1 ELSE 0 END) = 0')
                ->setParameter('metaKey', $metaKey)
                ->getQuery()
            ;
            
            /**
             * @var \App\Entity\User\User $user
             */
            foreach($query->getResult() as $user) {
                
                $property = new Property(
                    $metaKey, 
                    $pattern->get('value'), 
                    $pattern->get('mode')
                );

                $user->addProperty($property);

                $this->entityManager->persist($user);
            }

            $this->entityManager->flush();
        }

        $this->symfonyStyle->success('Users Property Updated');
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
