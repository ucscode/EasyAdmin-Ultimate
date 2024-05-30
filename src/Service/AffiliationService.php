<?php

namespace App\Service;

use App\Entity\User\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

class AffiliationService
{
    public const REQUEST_QUERY_KEY = 'ref';
    public const REQUEST_COOKIE_KEY = 'app_ref';
    private const TRAVERSE_PARENT = 0;
    private const TRAVERSE_CHILDREN = 1;

    protected Connection $connection;
    protected ClassMetadata $classMetaData;
    
    public function __construct(
        protected RequestStack $requestStack, 
        protected EntityManagerInterface $entityManager,
        protected ConfigurationService $configurationService
    )
    {
        $this->connection = $this->entityManager->getConnection();
        $this->classMetaData = $this->entityManager->getClassMetadata(User::class);
    }

    /**
     * Checks if the affiliation feature is enabled.
     *
     * @return bool Returns true if the affiliation feature is enabled, false otherwise.
     */
    public function isEnabled(): bool
    {
        return !!$this->configurationService->get('affiliation.enabled');
    }

    /**
     * Retrieves the referrer user from the current request, if available.
     *
     * @return User|null Returns the referrer user if found, null otherwise.
     */
    public function getRequestReferrer(): ?User
    {
        $referralId = 
            $this->requestStack->getCurrentRequest()->query->get(self::REQUEST_QUERY_KEY) ??
            $this->requestStack->getCurrentRequest()->cookies->get(self::REQUEST_COOKIE_KEY);

        return $referralId ? $this->entityManager->getRepository(User::class)->findOneBy(['uniqueId' => $referralId]) : null;
    }

    /**
     * Get children of a user at different depths
     * 
     * @param User $user The user to traverse
     * @param array $criteria The criteria to filter children
     * @return Result Doctrine DBAL Result containing all children
     */
    public function getChildren(User|int $user, array $criteria = []): Result
    {   
        $queryString = $this->getRecursionQuerySQL(
            $user instanceof User ? $user->getId() : $user, 
            self::TRAVERSE_CHILDREN,
            $this->getCriteriaCondition($criteria, self::TRAVERSE_CHILDREN) ?: 1
        );
        
       return $this->connection->prepare($queryString)->executeQuery();
    }

    /**
     * Get ancestors of a user
     * 
     * @param User $user The user to traverse
     * @param array $criteria The criteria to filter parents
     * @return Result Doctrine DBAL Result containing all parents
     */
    public function getAncestors(User|int $user, array $criteria = []): Result
    {
        $queryString = $this->getRecursionQuerySQL(
            $user instanceof User ? $user->getId() : $user,
            self::TRAVERSE_PARENT,
            $this->getCriteriaCondition($criteria, self::TRAVERSE_PARENT) ?: 1
        );
        
        return $this->connection->prepare($queryString)->executeQuery();
    }

    /**
     * Checks if a given user is a child of a reference user.
     *
     * @param User|int $user The base entity/node.
     * @param User|int $child The child to be tested for.
     * @return bool Returns true if the given user has the child specified, false otherwise.
     */
    public function hasChild(User|int $user, User|int $child): bool
    {
        return !empty($this->getChildren($user, ['entity' => $child])->rowCount());
    }

    /**
     * Checks if a given user is an ancestor of a reference user.
     *
     * @param User|int $user The base entity/node.
     * @param User|int $ancestor The parent to check for.
     * @return bool Returns true if the given user has the ancestor specified, false otherwise.
     */
    public function hasAncestor(User|int $user, User|int $ancestor): bool
    {
        return !empty($this->getAncestors($user, ['entity' => $ancestor])->rowCount());
    }

    /**
     * Checks if the target user is a child of the reference user.
     *
     * @param User $user The base entity/node.
     * @param User $parent The parent user to test against.
     * @return bool Returns true if the target user is a child of the parent user, false otherwise.
     */
    public function isChildOf(User|int $user, User|int $parent): bool
    {
        return $this->hasAncestor($user, $parent);
    }

    /**
     * Checks if the target user is a parent of the reference user.
     *
     * @param User|int $user The base entity/node to check.
     * @param User|int $child The child user to check against.
     * @return bool Returns true if the target user is a parent of the reference user, false otherwise.
     */
    public function isParentOf(User|int $user, User|int $child): bool
    {
        return $this->hasChild($user, $child);
    }

    /**
     * Check if a node/user has children. IE. Check if user is leaf node
     * 
     * @param User|int $reference   The reference user to check against
     * @return bool     Returns true if children exists, false otherwise.
     */
    public function hasChildren(User|int $user): bool
    {
        $simpleQuery = sprintf(
            "SELECT id FROM `%s` WHERE parent_id = %s",
            $this->classMetaData->getTableName(),
            $user instanceof User ? $user->getId() : $user
        );

        return !empty($this->connection->prepare($simpleQuery)->executeQuery()->rowCount());
    }

    /**
     * Get a recursive SQL Syntax that iterates over adjacency list descendants or ancestors
     * 
     * @param int $entityId             The anchor or node to begin iteration from
     * @param int $traversal            Whether to return SQL query for children or parent traversal
     * @param string $filterCondtion    The condition to filter the list of traversed result
     * 
     * @return string                   The recursive SQL Syntax
     */
    private function getRecursionQuerySQL(int $entityId, int $traversal, ?string $filterCondition = null): string
    {       
        $recursionQuery = "WITH RECURSIVE nodes AS (
            -- The Anchor Member 
            SELECT 
                anchor.id, 
                anchor.email,
                anchor.parent_id AS parentId, 
                0 AS depth
            FROM `@entity` anchor
            WHERE id = :entityId

            UNION ALL

            -- Recursive Member
            SELECT 
                kin.id, 
                kin.email,
                kin.parent_id as parentId, 
                nodes.depth + 1
            FROM `@entity` kin
            INNER JOIN nodes ON @traversalCondition
        )

        SELECT nodes.*
        FROM nodes
        WHERE id <> :entityId
        AND @filterCondition
        ORDER BY depth
        ";

        $traversalCondition = ($traversal === self::TRAVERSE_CHILDREN) ? "kin.parent_id = nodes.id" : "kin.id = nodes.parent_id";

        return strtr($recursionQuery, [
            '@entity' => $this->classMetaData->getTableName(),
            '@traversalCondition' => $traversalCondition,
            '@filterCondition' => $filterCondition ?: 1,
            ':entityId' => $entityId,
        ]);
    }

    /**
     * An option resolver to validate the option passed by user to filter traversal result
     * 
     * @param array $criteria   The array of data to traverse
     * @return array            The validated resolved option
     * @throws \Exception        If criteria contain invalid key or value
     */
    private function resolveCriteriaOptions(array $criteria): array
    {
        $defaultOptions = [
            'depth' => null, // the depth to get/return
            'entity' => null, // the identity of a child
            'minDepth' => null, // minimum depth to fetch
            'maxDepth' => null, // max depth to fetch
        ];

        $resolver = new OptionsResolver();
        $resolver->setDefaults($defaultOptions);

        foreach($defaultOptions as $key => $option) {
            $resolver
                ->setAllowedTypes($key, ['null', 'integer'])
                ->setAllowedValues($key, fn (?int $value) => $value === null || $value > 0);
        }
        
        $criteria = $resolver->resolve($criteria);
        
        if($criteria['maxDepth'] !== null) {
            Assert::greaterThanEq(
                $criteria['maxDepth'], 
                $criteria['minDepth'], 
                'The option "maxDepth" should not be less than "minDepth"'
            );
        }

        return $criteria;
    }

    /**
     * Generates the condition string used to filter traversal result
     * 
     * @param array $criteria   The criteria to confirm
     * @return string           The "where" clause string to filter the result
     */
    private function getCriteriaCondition(array $criteria): ?string
    {
        // convert user entity to entity id
        if(!empty($criteria['entity']) && $criteria['entity'] instanceof User) {
            $criteria['entity'] = $criteria['entity']->getId();
        }

        $criteria = $this->resolveCriteriaOptions($criteria);

        $condition = array_filter([
            "depth >= %s" => $criteria['minDepth'] ?? null,
            "depth <= %s" => $criteria['maxDepth'] ?? null,
            "depth = %s" => $criteria['depth'] ?? null,
            "id = %s" => $criteria['entity'] ?? null, 
        ]);

        $result = array_map(
            fn ($value, $key) => sprintf($value, $key), 
            array_keys($condition), 
            array_values($condition)
        );

        return trim(implode(" AND ", $result)) ?: null;
    }
}