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
    name: 'uss:init',
    description: 'Initialize User Synthetics Application',
    hidden: false,
)]
class InitCommand extends Command
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Creating Admin Configuration',
            ''
        ]);
        
        foreach(SystemConfig::getConfigurationStructure() as $key => $context) {
            
            $config = $this->entityManager->getRepository(Configuration::class)->findOneBy([
                'metaKey' => $key
            ]);

            if(!$config) {

                try {
                    
                    $config = (new Configuration())
                        ->setMetaKey($key)
                        ->setMetaValue($context['value'])
                        ->setBitwiseMode($context['mode'])
                    ;

                    $this->entityManager->persist($config);

                    $output->writeln([
                        sprintf('[<info>%s</info>] = %s', $key, $context['value']),
                        ''
                    ]);

                } catch(Exception $exception) {

                    $output->writeln(sprintf(
                        "<error>%s on %s:%s</error>", 
                        $exception->getMessage(),
                        $exception->getFile(),
                        $exception->getLine(),
                    ));

                    return Command::FAILURE;

                }
            }
        }

        $this->entityManager->flush();

        $output->writeln('<info>Initialization Successful</info>');

        return Command::SUCCESS;
    }
}