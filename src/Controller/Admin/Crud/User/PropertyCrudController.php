<?php

namespace App\Controller\Admin\Crud\User;

use App\Controller\Admin\Abstracts\AbstractAdminCrudController;
use App\Entity\User\Property;
use App\Entity\User\User;
use App\Enum\ModeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PropertyCrudController extends AbstractAdminCrudController
{
    public const PROPERTY_KEY = 'metaValue';

    public function __construct(protected EntityManagerInterface $entityManager)
    {

    }

    public static function getEntityFqcn(): string
    {
        return Property::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle(Crud::PAGE_INDEX, 'User Properties')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modify User Property')
        ;
    }

    /**
     * @return \Generator
     */
    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('user')
            ->setDisabled()
        ;

        yield TextField::new('metaKey', 'Property')
            ->setDisabled()
        ;

        // if page is form page

        if(in_array($pageName, [Crud::PAGE_EDIT])) {
            yield $this->getDynamicFormFields();
            return;
        };

        yield Field::new('metaValueAsString', 'Value')
            ->formatValue(
                function (mixed $value, Property $entity) {
                    // Write your condition to format values in INDEX page
                    return $value;
                }
            );
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::BATCH_DELETE, Action::NEW, Action::DELETE)
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action
                    ->displayIf(function (Property $entity) {
                        // Write your condition to hide edit button
                        return $entity->hasMode(ModeEnum::WRITE->value);
                    });
            })
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        /**
         * @var \Symfony\Component\HttpFoundation\Request
         * */
        $request = $this->getContext()->getRequest();

        /**
         * @var ?int
         */
        $userId = $request->query->get('userId');

        if(!$userId) {
            throw new \RuntimeException(sprintf(
                'Unable to retrieve "%s" list. The user identifier is not specified in the request.',
                Property::class
            ));
        }

        /**
         * @var ?\App\Repository\User\UserRepository
         */
        $userRepository = $this->entityManager->getRepository(User::class);
        
        /**
         * @var ?\App\Entity\User\User
         */
        $userEntity = $userRepository->find($userId);

        if(!$userEntity) {
            throw new \RuntimeException(sprintf(
                '%s::id = %s does not exist in the database',
                User::class,
                $userId
            ));
        }

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.user = :user')
            ->andWhere('entity.mode >= :mode')
            ->setParameter('user', $userEntity)
            ->setParameter('mode', ModeEnum::READ->value)
        ;
    }

    protected function getDynamicFormFields(): FieldInterface
    {
        /**
         * @var Property $entity
         * */
        $entity = $this->getContext()->getEntity()?->getInstance();

        if(!$entity->hasMode(ModeEnum::WRITE)) {
            throw new \RuntimeException(sprintf(
                'The user property "%s" is readonly and cannot be modified from GUI',
                $entity->getMetaKey()
            ));
        }

        $metaKey = $entity->getMetaKey();

        /**
         * @var Field $field
         * */
        $field = $this->getMetaValueFieldTypes()[$metaKey] ?? TextField::new(self::PROPERTY_KEY);

        $field->setLabel('Value');

        return $field;
    }

    /**
     * Edit the array within the function to match your project preference
     *
     * @return array
     */
    protected function getMetaValueFieldTypes(): array
    {
        return [
            'balance' => MoneyField::new(self::PROPERTY_KEY)
                ->setCurrency('USD'),
        ];
    }
}
