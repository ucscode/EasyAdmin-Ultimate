<?php

namespace App\Controller\Base\Trait;

use App\Service\ConfigurationService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

trait BaseDashboardControllerTrait
{
    public function __construct(
        protected AdminUrlGenerator $adminUrlGenerator,
        protected EntityManagerInterface $entityManager,
        protected ConfigurationService $configurationService,
        protected UserPasswordHasherInterface $userPasswordHasher,
    ) {
        // constructor
    }
}
