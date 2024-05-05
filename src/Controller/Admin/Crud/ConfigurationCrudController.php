<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Abstracts\AbstractAdminCrudController;
use App\Entity\Configuration;
use App\Enum\ModeEnum;
use App\Service\ConfigurationService;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use function Symfony\Component\String\u;

class ConfigurationCrudController extends AbstractAdminCrudController
{
    public function __construct(
        protected EntityManagerInterface $entityManager, 
        protected ConfigurationService $configurationService
    ) {
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
                ->formatValue(function ($value) {
                    $split = array_map(function ($value) {
                        return ucwords(preg_replace("/[_\-]/", " ", $value));
                    }, explode(".", $value));
                    $split[0] = sprintf('<span class="fw-semibold text-info">%s</span>', strtoupper($split[0]));
                    return implode('<i class="fas fa-caret-right mx-1"></i>', $split);
                });

            yield TextField::new('metaValueAsString', 'Value')
                ->formatValue(function (string $value, Configuration $entity) {
                    $context = $this->configurationService->getConfigurationStructure($entity->getMetaKey(), 'format');
                    return $context ? call_user_func($context, $entity) : u($value)->truncate(71, '&hellip;');
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
            throw new \RuntimeException('New configuration cannot be created from GUI');
        }

        if(!$entity->hasBitwiseMode(ModeEnum::WRITE)) {
            throw new \Exception(sprintf('`%s` configuration is readonly and cannot be modified from GUI', $entity->getMetaKey()));
        }

        $structure = $this->configurationService->getConfigurationStructure($entity->getMetaKey());

        return $structure['field'];
    }
}