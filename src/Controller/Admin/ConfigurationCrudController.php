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
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
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
        if(!$this->isFormPage($pageName)) {

            yield TextField::new('metaKey', 'Name')
                ->formatValue(function($value) {
                    return $this->formatLabel($value);
                });
                
            yield TextField::new('metaValueAsString', 'value');
            
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
        
        return $this->getConfigurationField($entity);
    }

    private function getConfigurationField(Configuration $entity): FieldInterface
    {
        $metaKey = $entity->getMetaKey();
        $name = 'metaValue';

        $field = match($metaKey) {
            'app.slogan' => TextareaField::new($name),
            'app.description' => TextareaField::new($name),
            'office.email' => EmailField::new($name),
            'office.phone' => TelephoneField::new($name),
            'office.address' => TextareaField::new($name),
            'test.key' => BooleanField::new($name),
            default => TextField::new($name),
        };

        $field->setLabel($this->formatLabel($metaKey));

        return $field;
    }

    private function formatLabel(?string $label): string
    {
        return empty($label) ? 'Value' : ucwords(implode(' ', explode(".", $label)));
    }

    private function isFormPage(string $pageName): bool
    {
        return in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT]);
    }

}
