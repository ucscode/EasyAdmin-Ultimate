<?php

namespace App\Controller\Admin;

use App\Entity\Configuration;
use App\Enum\ModeEnum;
use App\Immutable\SystemConfig;
use App\Service\PrimaryTaskService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfigurationCrudController extends AbstractCrudController
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected PrimaryTaskService $primaryTaskService,
    )
    {   
        // constructor
    }

    public static function getEntityFqcn(): string
    {
        return Configuration::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE)
            ->disable(Action::NEW, Action::DELETE, Action::DETAIL)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        if(!in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT])) {

            yield TextField::new('metaKey', 'Name')
                ->formatValue(function($value) {
                    return ucwords(str_replace('.', " ", $value));
                });
                
            yield TextField::new('metaValueAsString', 'value')
                ->formatValue(function($value, $entity) {
                    // return your formated value here
                    return $this->primaryTaskService->truncateText($value);
                })
            ;
            
            return;
        }

        yield $this->getDynamicMetaValueField($pageName);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.bitwiseMode >= :mode')
            ->setParameter('mode', ModeEnum::READ->value)
        ;
    }

    protected function getDynamicMetaValueField(string $pageName): FieldInterface
    {
        $entity = $this->getContext()->getEntity()->getInstance();

        if(!$entity) {
            throw new \RuntimeException('Configuration cannot be created from GUI');
        }

        if($entity->getBitwiseMode() < ModeEnum::WRITE->value) {
            throw new \Exception(sprintf('%s configuration cannot be modified from GUI', $entity->getMetaKey()));
        }
        
        $structure = SystemConfig::getConfigurationStructure($entity->getMetaKey());

        return $structure['field'];
    }
}
