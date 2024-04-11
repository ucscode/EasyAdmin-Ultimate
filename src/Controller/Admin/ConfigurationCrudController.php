<?php

namespace App\Controller\Admin;

use App\Entity\Configuration;
use App\Immutable\SystemConfig;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfigurationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Configuration::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('metaKey', 'Name')
            ->setDisabled(true);

        yield $this->getDynamicMetaValueField($pageName);
    }

    protected function getDynamicMetaValueField(string $pageName): FieldInterface
    {
        if(in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL])) {
            return TextField::new('metaValueAsString', 'value');
        }

        $entity = $this->getContext()->getEntity()->getInstance();
        $metaField = TextField::new('metaValue', 'Value');

        if($entity) {
            foreach(SystemConfig::ADMIN_CONFIG_STRUCTURE as $metaKey => $config) {
                if($metaKey === $entity->getMetaKey()) {
                    if(!empty($config['field'])) {
                        $metaField = $config['field']::new('metaValue', $config['label'] ?? 'Value');
                    }
                    break;
                }
            }
        }
        
        return $metaField;
    }
}
