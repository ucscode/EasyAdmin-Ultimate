<?php

namespace App\Controller\Base\Trait;

use App\Service\ConfigurationService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Ucscode\KeyGenerator\KeyGenerator;

trait BaseDashboardControllerTrait
{
    protected KeyGenerator $keyGenerator;

    public function __construct(
        protected AdminUrlGenerator $adminUrlGenerator,
        protected EntityManagerInterface $entityManager,
        protected ConfigurationService $configurationService,
        protected UserPasswordHasherInterface $userPasswordHasher,
    ) {
        $this->keyGenerator = new KeyGenerator();
    }
}
