<?php

namespace App\Configuration\Factory;

use App\Component\Traits\SingletonTrait;
use App\Configuration\AbstractConfigurationPattern;
use App\Configuration\ValueObject\UserPropertyFieldVO;
use App\Constants\ModeConstants;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\String\UnicodeString;

/**
 * @method UserPropertyFieldVO getItem(string $name)
 * @method self setItem(string $name, UserPropertyFieldVO $value)
 * @method UserPropertyFieldVO[] getItems()
 */
class UserPropertyFieldVOFactory extends AbstractConfigurationPattern
{
    use SingletonTrait;
    
    public static function getItemFqcn(): string
    {
        return UserPropertyFieldVO::class;
    }

    protected function configureItems(): void
    {
        $this->setItem('first_name', new UserPropertyFieldVO());

        $this->setItem('last_name', new UserPropertyFieldVO());

        $this->setItem(
            'about', 
            (new UserPropertyFieldVO())
                ->setDescription('Please enter your user biography here')
                ->setFieldFqcn(TextareaField::class)
        );

        $this->setItem(
            'balance',
            (new UserPropertyFieldVO())
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
            (new UserPropertyFieldVO())
                ->setFieldFqcn(BooleanField::class)
                ->setValue(false)
                ->setMode(ModeConstants::NO_PERMISSION)
        );
    }

    /**
     * @param UserPropertyFieldVO $value
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