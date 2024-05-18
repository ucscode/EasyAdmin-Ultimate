<?php

namespace App\Controller\Admin\Crud\User;

use App\Controller\Admin\Abstracts\AbstractAdminCrudController;
use App\Controller\Admin\DashboardController;
use App\Entity\User\Property;
use App\Entity\User\User;
use App\Enum\ModeEnum;
use App\Utils\Stateless\CaseConverter;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

class PropertyCrudController extends AbstractAdminCrudController
{
    protected ?User $propertyOwner;

    public function __construct(
        protected EntityManagerInterface $entityManager, 
        protected RequestStack $requestStack,
        protected AdminUrlGenerator $adminUrlGenerator,
        protected KernelInterface $kernel
    )
    {
        $this->setPropertyOwner();
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
            ->formatValue(fn ($value) => ucwords(CaseConverter::toSentenceCase($value)))
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

                    ->displayIf(fn (Property $entity) => $entity->hasMode(ModeEnum::WRITE))

                    ->linkToUrl(function(Property $entity) {
                        
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
                'You do not have permission to modify the "%s" property',
                $entity->getMetaKey()
            ));
        }

        $configs = $this->getMetaValueFieldConfigs($entity->getMetaKey());

        return $configs['field'];
    }

    /**
     * Edit the array within the function to match your project preference
     *
     * @return FieldInterface
     */
    protected function getMetaValueFieldConfigs(string $metaKey = null): ?array
    {
        $propertyConfigFile = sprintf('%s/src/Config/UserPropertyConfig.php', $this->kernel->getProjectDir());
        $closure = require $propertyConfigFile;
        $configuration = $closure();

        foreach($configuration as $key => &$config) {
            $config['label'] ??= ucwords(CaseConverter::toSentenceCase($key));
            $config['value'] ??= null;
            $config['mode'] ??= ModeEnum::READ->value|ModeEnum::WRITE->value;
            $config['field'] ??= TextField::class;

            if(!in_array(FieldInterface::class, \class_implements($config['field']))) {
                throw new \InvalidArgumentException(sprintf(
                    '% configuration; %s field must implement %s',
                    Property::class,
                    $key,
                    FieldInterface::class
                ));
            }
            
            $config['field'] = $config['field']::new('metaValue');
            $config['field']->setLabel($config['label']);

            if(is_callable($config['configure_field'] ?? null)) {
                call_user_func($config['configure_field'], $config['field']);
                unset($config['configure_field']);
            }
        }

        return !$metaKey ? $configuration : ($configuration[$metaKey] ?? null);
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
