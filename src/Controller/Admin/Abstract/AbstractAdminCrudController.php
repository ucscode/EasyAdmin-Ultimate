<?php

namespace App\Controller\Admin\Abstract;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AbstractAdminCrudController extends AbstractCrudController
{
    public function __construct(
        protected AdminUrlGenerator $adminUrlGenerator,
        protected EntityManagerInterface $entityManager,
        protected UserPasswordHasherInterface $userPasswordHasher
    )
    {
        // constructor
    }
}