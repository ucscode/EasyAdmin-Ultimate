<?php

namespace App\Controller\General\Trait;

use App\Entity\Configuration;
use App\Service\PrimaryTaskService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

trait GeneralDashboardControllerTrait
{
    public function __construct(
        protected AdminUrlGenerator $adminUrlGenerator,
        protected EntityManagerInterface $entityManager,
        protected UserPasswordHasherInterface $userPasswordHasher,
        protected PrimaryTaskService $primaryTaskService
    )
    {
        // constructor
    }
    
    protected function getConfigurationValue(string $metaKey, ?string $default = null): ?string
    {
        $repository = $this->entityManager->getRepository(Configuration::class);
        /**
         * @var Configuration
         */
        $config = $repository->findOneBy(['metaKey' => $metaKey]);
        $value = $config?->getMetaValueAsString() ?? $default;
        
        return $value;
    }
}