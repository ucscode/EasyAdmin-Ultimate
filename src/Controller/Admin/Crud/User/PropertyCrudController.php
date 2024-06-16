<?php

namespace App\Controller\Admin\Crud\User;

use App\Configuration\Factory\UserPropertyFieldDesignFactory;
use App\Constants\ModeConstants;
use App\Controller\Admin\Abstracts\AbstractAdminCrudController;
use App\Controller\Admin\DashboardController;
use App\Entity\User\Property;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

class PropertyCrudController extends AbstractAdminCrudController
{
    protected ?User $propertyOwner;
    protected UserPropertyFieldDesignFactory $userPropertyFieldManager;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected RequestStack $requestStack,
        protected AdminUrlGenerator $adminUrlGenerator,
        protected KernelInterface $kernel
    ) {
        $this->setPropertyOwner();
        $this->userPropertyFieldManager = UserPropertyFieldDesignFactory::getInstance();
    }

    public static function getEntityFqcn(): string
    {
        return Property::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $indexTitle = ucfirst(sprintf('%s [properties]', $this->propertyOwner->getEmail()));
        $editTitle = ucfirst(sprintf('%s [edit property]', $this->propertyOwner->getEmail()));

        return $crud
            ->showEntityActionsInlined()
            ->setPageTitle(Crud::PAGE_INDEX, $indexTitle)
            ->setPageTitle(Crud::PAGE_EDIT, $editTitle)
            ->overrideTemplate('crud/edit', 'admin/crud/edit/user_property.html.twig')
        ;
    }

    /**
     * @return \Generator
     */
    public function configureFields(string $pageName): iterable
    {
        if(in_array($pageName, [Crud::PAGE_EDIT])) {
            yield $this->getDynamicFormFields();
            return;
        };

        yield TextField::new('metaKey', 'Property')
            ->setDisabled()
            ->formatValue(fn (string $value) => $this->userPropertyFieldManager->getItem($value)->getLabel())
        ;

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

                    ->displayIf(fn (Property $entity) => $entity->hasMode(ModeConstants::WRITE))

                    ->linkToUrl(function (Property $entity) {

                        return $this->adminUrlGenerator
                            ->setDashboard(DashboardController::class)
                            ->setController(PropertyCrudController::class)
                            ->setAction(Action::EDIT)
                            ->set('userId', $entity->getUser()->getId())
                            ->setEntityId($entity->getId())
                            ->generateUrl()
                        ;
                    })
                ;
            })

        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if(!$this->propertyOwner) {
            throw new \RuntimeException('Property owner does not exist in the database');
        }

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.user = :user')
            ->andWhere('entity.mode >= :mode')
            ->setParameter('user', $this->propertyOwner)
            ->setParameter('mode', ModeConstants::READ)
        ;
    }

    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        if(Crud::PAGE_EDIT === $responseParameters->get('pageName')) {
            /** @var Property */
            $property = $responseParameters->get('entity')->getInstance();
            $responseParameters->set('fieldConfig', $this->userPropertyFieldManager->getItem($property->getMetaKey()));
        }

        return $responseParameters;
    }

    protected function getDynamicFormFields(): FieldInterface
    {
        /**
         * @var Property $entity
         * */
        $entity = $this->getContext()->getEntity()?->getInstance();

        if(!$entity->hasMode(ModeConstants::WRITE)) {
            throw new \RuntimeException(sprintf(
                'You do not have permission to modify the "%s" property',
                $entity->getMetaKey()
            ));
        }

        $fieldItem = $this->userPropertyFieldManager->getItem($entity->getMetaKey());

        return $fieldItem->getFieldInstance();
    }

    private function setPropertyOwner(): void
    {
        $userId = $this->requestStack->getCurrentRequest()->query->get('userId');

        if(empty($userId)) {
            throw new \RuntimeException(sprintf(
                'Unable to retrieve "%s" list. The user identifier is not specified in the request.',
                Property::class
            ));
        }

        /**
         * @var \App\Repository\User\UserRepository
         */
        $userRepository = $this->entityManager->getRepository(User::class);

        $this->propertyOwner = $userRepository->find($userId);
    }
}
