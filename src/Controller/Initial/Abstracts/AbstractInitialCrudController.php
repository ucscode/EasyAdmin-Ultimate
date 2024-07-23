<?php

namespace App\Controller\Initial\Abstracts;

use App\Controller\Initial\Traits\InitialControllerTrait;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * Base CRUD controller for managing entities across both Admin and User dashboards.
 *
 * This abstract class provides common functionalities required for entity management
 * within the administrative and user-facing dashboards, ensuring a consistent
 * CRUD interface for both contexts.
 *
 * @author Ucscode
 */
abstract class AbstractInitialCrudController extends AbstractCrudController
{
    use InitialControllerTrait;

    protected function disableAllActions(Actions $actions, string|array $exempt = []): Actions
    {
        $defaults = [
            Action::NEW,
            Action::DELETE,
            Action::DETAIL,
            Action::EDIT,
        ];

        !is_string($exempt) ?: $exception = [$exempt];

        foreach(array_diff($defaults, $exception) as $action) {
            $actions->disable($action);
        }

        return  $actions;
    }
    
    protected function onlyUserEntities(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder
            ->andWhere('entity.user = :user')
            ->setParameter('user', $this->getUser())
        ;
    }

    /**
     * @param array<string,string> $orderBy Sort an entity by [property => ASC|DESC] 
     */
    protected function sortIndexQueryEntities(QueryBuilder $queryBuilder, array $orderBy = ['id' => 'DESC']): QueryBuilder
    {
        /**
         * @var \Symfony\Component\HttpFoundation\Request $request
         */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        
        $criteria = Criteria::create()->orderBy(array_merge($orderBy, $request->query->all('sort')));

        return $queryBuilder->addCriteria($criteria);
    }
}
