<?php

namespace App\Command;

use App\Entity\User\Property;
use App\Entity\User\User;
use App\Service\Configuration\UserPropertyFieldManager;
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

    /**
     * The command entry point
     *
     * This method is called by symfony when run through "php bin/console ..."
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $this->symfonyStyle = new SymfonyStyle($this->input, $this->output);

        $this->symfonyStyle->info(sprintf('APP_ENV=%s', $this->kernel->getEnvironment()));

        try {

            // $this->updateComposerPackages();
            $this->computeAssetMapperResource();
            $this->updateUserProperties();

        } catch(Exception $exception) {
            $this->symfonyStyle->error(
                sprintf("%s on %s:%s", $exception->getMessage(), $exception->getFile(), $exception->getLine())
            );

            return Command::FAILURE;
        }

        $this->symfonyStyle->success('User Synthetics Initialization Completed');

        return Command::SUCCESS;
    }

    /**
     * $ composer update
     *
     * Update project dependencies to the latest version and modifies the "composer.lock" file
     * to reflect the new versions of the packages that have been installed.
     */
    protected function updateComposerPackages(): void
    {
        $this->symfonyStyle->title("Updating Composer Packages");
        $this->execBashCommand(['composer', 'update']);
        $this->symfonyStyle->success("Composer Packages Revised");
    }

    /**
     * initialize import map
     *
     * Install import map packages and compile them when on production environment
     */
    protected function computeAssetMapperResource(): void
    {
        $this->symfonyStyle->title("Initializing Asset Mapper");
        $this->execBashCommand(['php', 'bin/console', 'importmap:install']);

        ($this->kernel->getEnvironment() == self::ENV_DEV) ?:
        $this->execBashCommand(['php', 'bin/console', 'asset-map:compile']);

        $this->symfonyStyle->success('Asset Mapper Initialized');
    }

    /**
     * Update user property configurations
     *
     * When new properties are added to user property pattern after compilation, only newly registered
     * users will receive the new property. To ensure the property is available to all users, this
     * will iterate the patterns and new patterns will be added for users who don't have the property
     */
    protected function updateUserProperties(): void
    {
        $this->symfonyStyle->title('Updating User Properties');

        $userRepository = $this->entityManager->getRepository(User::class);

        foreach(UserPropertyFieldManager::getInstance()->getItems() as $metaKey => $fieldConfig) {

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
                    $fieldConfig->getValue(),
                    $fieldConfig->getMode()
                );

                $user->addProperty($property);

                $this->entityManager->persist($user);
            }

            $this->entityManager->flush();
        }

        $this->symfonyStyle->success('Users Property Updated');
    }

    private function execBashCommand(array $command): void
    {
        $process = new Process($command);
        $process->run();

        if(!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->symfonyStyle->text($process->getOutput());
    }
}
