<?php

namespace App\Service;

use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AffiliationService
{
    public const QUERY_KEY = 'ref';
    public const COOKIE_KEY = 'app_ref';

    public function __construct(
        protected RequestStack $requestStack, 
        protected EntityManagerInterface $entityManager,
        protected ConfigurationService $configurationService
    )
    {
        
    }

    public function isEnabled(): bool
    {
        return !!$this->configurationService->get('affiliation.enabled');
    }

    public function getRequestReferrer(): ?User
    {
        if($this->isEnabled()) {
            $referralId = 
                $this->requestStack->getCurrentRequest()->query->get(self::QUERY_KEY) ??
                $this->requestStack->getCurrentRequest()->cookies->get(self::COOKIE_KEY)
            ;

            return $referralId ? $this->entityManager->getRepository(User::class)->findOneBy(['uniqueId' => $referralId]) : null;
        }

        return null;
    }

    /**
     * Get children of a user at different levels
     * 
     * @param User $user The user to traverse
     * @param null|int $level The level to fetch
     * @return array[] Each level containing an array of children
     */
    public function getChildren(User $user, ?int $level = null): array
    {
        return [];
    }

    /**
     * Get parent of a user up to the root or to a specified level
     * 
     * @param User $user The user to traverse
     * @param null|int $level The level to fetch
     * @return User[]|User|null The parent list or parent entity
     */
    public function getParents(User $user, ?int $level = null): User|array|null
    {
        $builder = $this->entityManager->getRepository(User::class)->createQueryBuilder('U')

        ;
        dd($builder);
        return null;
    }

    /**
     * @return ?int The level of the child or null if the child does not exists
     */
    public function UserHasChild(User $child, User $reference): ?int
    {
        return 1;
    }

    /**
     * @return ?int The level of the parent or null if the parent does not exist
     */
    public function UserHasParent(User $parent, User $reference): ?int
    {
        return 1;
    }

    public function isChildOf(User $target, User $reference): bool
    {
        return false;
    }

    public function isParentOf(User $target, User $reference): int
    {
        return false;
    }
}