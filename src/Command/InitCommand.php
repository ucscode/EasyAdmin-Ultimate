<?php

namespace App\Command;

use App\Entity\Configuration;
use App\Enum\ModeEnum;
use App\Immutable\SystemConfig;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'uss:initialize',
    description: 'Initialize User Synthetics Application',
    hidden: false,
)]
class InitCommand extends Command
{
    protected InputInterface $input;
    protected OutputInterface $output;

    public function __construct(protected EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        try {

            $this->overloadAdminConfiguration();
            $this->computeAssetMapperResource(); 

        } catch(Exception $exception) {

            $output->writeln(sprintf(
                "<error>%s on %s:%s</error>", 
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
            ));

            return Command::FAILURE;

        }

        return Command::SUCCESS;
    }

    protected function overloadAdminConfiguration(): void
    {
        $this->output->writeln([
            'Creating Admin Configuration',
            ''
        ]);
        
        foreach(SystemConfig::getConfigurationStructure() as $key => $context) {
            
            $config = $this->entityManager->getRepository(Configuration::class)->findOneBy(['metaKey' => $key]);

            if(!$config) {
                    
                $config = (new Configuration())
                    ->setMetaKey($key)
                    ->setMetaValue($context['value'])
                    ->setBitwiseMode($context['mode'])
                ;

                $this->entityManager->persist($config);

                $this->output->writeln([
                    sprintf('[<info>%s</info>] = %s', $key, $context['value']),
                    ''
                ]);
            }
        }

        $this->entityManager->flush();

        $this->output->writeln('<info>Initialization Successful</info>');
    }

    protected function computeAssetMapperResource(): void
    {
        // php bin/console importmap:install

        # If env == prod
        // php bin/console asset-map:compile
    }
}