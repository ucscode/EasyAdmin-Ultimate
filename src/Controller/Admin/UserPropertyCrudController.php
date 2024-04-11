<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Abstract\AbstractAdminCrudController;
use App\Entity\User;
use App\Entity\UserProperty;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserPropertyCrudController extends AbstractAdminCrudController
{
    const PROPERTY_KEY = 'metaValue';

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
        ;
    }

    public static function getEntityFqcn(): string
    {
        return UserProperty::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('user')
            ->setDisabled()
        ;

        yield TextField::new('metaKey')
            ->setDisabled()
        ;

        // If crud is EDIT page, use dynamic fields. Otherwise, use regular field

        yield in_array($pageName, [Crud::PAGE_EDIT]) ? 
            $this->getDynamicFields() : 
            Field::new('metaValueAsString')
                ->formatValue(function(mixed $value, UserProperty $entity) {
                    return $value . '-';
                });
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::BATCH_DELETE, Action::NEW, Action::DELETE)
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $request = $this->getContext()->getRequest();
        $userId = $request->query->get('userId');

        if(!$userId) {
            throw new \RuntimeException(sprintf(
                'Unable to retrieve "%s" list. The user identifier is not specified in the request.',
                UserProperty::class
            ));
        }

        $userRepository = $this->entityManager->getRepository(User::class);
        $userEntity = $userRepository->find($userId);

        if(!$userEntity) {
            throw new \RuntimeException(sprintf(
                'The "%s" entity with "id = %s" does not exist in the database. The entity may have been deleted by mistake or by a "cascade={"remove"}" operation executed by Doctrine.',
                User::class,
                $userId
            ));
        }

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.user = :user')
            ->setParameter('user', $userEntity);
    }

    public function getDynamicFields(): FieldInterface
    {
        $entity = $this->getContext()->getEntity()?->getInstance();
        $metaKey = $entity->getMetaKey();
        $field = $this->getDistinctPropertyFields()[$metaKey] ?? TextField::new(self::PROPERTY_KEY);
        return $field;
    }

    protected function getDistinctPropertyFields(): array
    {
        return [
            'balance' => MoneyField::new(self::PROPERTY_KEY)
                ->setCurrency('USD'),
        ];
    }
}
