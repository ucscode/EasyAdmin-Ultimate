<?php

namespace App\Command;

use App\Entity\User;
use App\Enums\RolesEnum;
use App\Service\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'eau:create-user',
    description: 'Creates a new (verified) user'
)]
class EauCreateUserCommand extends Command
{
    public function __construct(protected EntityManagerInterface $entityManager, protected UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.')
            ->addArgument('role', InputArgument::OPTIONAL, 'The default role of the user')    
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = trim($input->getArgument('email'));
        $password = trim($input->getArgument('password'));
        $role = trim($input->getArgument('role') ?: RolesEnum::USER->value);

        $user = new User();
        
        $user
            ->setEmail($email)
            ->setRoles([$role])
            ->setPassword($this->passwordHasher->hashPassword($user, $password))
            ->setReferralCode(Util::generateReferralCode())
            ->setVerified(true)
        ;

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Admin user created successfully.');

        return Command::SUCCESS;
    }
}
