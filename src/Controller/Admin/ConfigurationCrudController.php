<?php

namespace App\Controller\Admin;

use App\Entity\Configuration;
use App\Enum\ModeEnum;
use App\Immutable\SystemConfig;
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
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        
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
        yield TextField::new('metaKey', 'Name')
            ->setDisabled(true)
            ->formatValue(function($value) {
                return $this->formatLabel($value);
            });

        yield $this->getDynamicMetaValueField($pageName)
            
        ;
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
        if(in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL])) {
            return TextField::new('metaValueAsString', 'value');
        }

        $configurations = $this->entityManager->getRepository(Configuration::class)->findAll();

        $configurations = array_filter($configurations, function(Configuration $config) {
            return $config->hasBitwiseMode(ModeEnum::READ);
        });

        dd($configurations);
        
        

        // $entity = $this->getContext()->getEntity()->getInstance();
        // $metaField = TextField::new('metaValue', $this->formatLabel($entity?->getMetaKey()));

        // if($entity) {
        //     foreach(SystemConfig::ADMIN_CONFIG_STRUCTURE as $metaKey => $config) {
        //         if($metaKey === $entity->getMetaKey()) {
        //             if(!empty($config['field'])) {
        //                 $label = $config['label'] ?? $entity->getMetaKey();
        //                 $metaField = $config['field']::new('metaValue', $this->formatLabel($label));
        //             }
        //             break;
        //         }
        //     }
        // }
        
        return $metaField;
    }

    protected function formatLabel(?string $label): string
    {
        return empty($label) ? 'Value' : ucwords(implode(' ', explode(".", $label)));
    }
}
