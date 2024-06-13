<?php

namespace App\Service\Configuration;

use App\Component\Traits\SingletonTrait;
use App\Constants\ModeConstants;
use App\Model\Configuration\UserPropertyField;
use App\Service\Abstracts\AbstractConfigurationPattern;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\String\UnicodeString;

/**
 * @method UserPropertyField getItem(string $name)
 * @method self setItem(string name, UserPropertyField $value)
 * @method UserPropertyField[] getItems()
 */
class UserPropertyFieldManager extends AbstractConfigurationPattern
{
    use SingletonTrait;
    
    public static function getItemFqcn(): string
    {
        return UserPropertyField::class;
    }

    protected function configureItems(): void
    {
        $this->setItem('first_name', new UserPropertyField());

        $this->setItem('last_name', new UserPropertyField());

        $this->setItem(
            'about', 
            (new UserPropertyField())
                ->setDescription('Please enter your user biography here')
                ->setFieldFqcn(TextareaField::class)
        );

        $this->setItem(
            'balance',
            (new UserPropertyField())
                ->setLabel('Balance (USD)')
                ->setValue(0)
                ->setFieldFqcn(MoneyField::class)
                ->setDescription('User balance are saved as integer to avoid rounding errors! Therefore 9 USD will be saved as 900')
                ->setConfig(function(MoneyField $field) {
                    $field->setCurrency('USD');
                })
        );

        $this->setItem(
            'has_premium_account',
            (new UserPropertyField())
                ->setFieldFqcn(BooleanField::class)
                ->setValue(false)
                ->setMode(ModeConstants::NO_PERMISSION)
        );
    }

    /**
     * @param UserPropertyField $value
     */
    protected function normalizeItem(string $name, $value): void
    {
        $value->setName($name);

        if(!$value->getLabel()) {
            $label = (new UnicodeString($name))->snake()->replace('_', ' ')->title(true);
            $value->setLabel($label);
        }

        $fieldFqcn = $value->getFieldFqcn();
        $value->setFieldInstance($fieldFqcn::new('metaValue', $value->getLabel()));
        
        if($value->getConfig()) {
            ($value->getConfig())($value->getFieldInstance());
        }
    }
}