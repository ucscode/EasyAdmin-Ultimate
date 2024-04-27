<?php

namespace App\Utils;

use App\Enum\ModeEnum;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ConfigUtils
{
    private const FIELD_NAME = 'metaValue';

    public static function getConfigurationStructure(?string $metaKey = null): ?array
    {
        $configuration = [

            'app.name' => [
                'value' => 'User Synthetics',
                'field' => TextField::new(self::FIELD_NAME)
                    ->setRequired(true)
                    ->setFormTypeOption('constraints', new NotBlank())
                ,
            ],

            'app.logo' => [
                'value' => 'http://ucscode.com/common/images/origin.png',
                'field' => ImageField::new(self::FIELD_NAME)
                    ->setUploadDir('public/assets/images/system')
                    ->setBasePath('assets/images/system'),
            ],

            'app.slogan' => [
                'value' => 'Your premier destination for creating stunning and effective websites.',
                'field' => TextareaField::new(self::FIELD_NAME),
            ],

            'app.description' => [
                'value' => 'Our comprehensive suite of services covers every aspect of website creation, 
                from concept and design to development and launch. We work closely with our clients to understand 
                their goals, audience, and brand identity, ensuring that every website we create reflects their 
                vision and objectives.',
                'field' => TextareaField::new(self::FIELD_NAME),
            ],

            'office.email' => [
                'value' => 'office@example.com',
                'field' => EmailField::new(self::FIELD_NAME),
            ],

            'office.phone' => [
                'value' => '+1 212-555-0123',
                'field' => TelephoneField::new(self::FIELD_NAME),
            ],

            'office.address' => [
                'value' => "123 Main Street \nAnytown, CA 12345 \nUnited States",
                'field' => TextareaField::new(self::FIELD_NAME),
            ],

            'test.key' => [
                'value' => false,
                'field' => BooleanField::new(self::FIELD_NAME),
            ],
        ];

        return self::traverseConfigContext($configuration, $metaKey);
    }

    private static function traverseConfigContext(array &$configuration, ?string $metaKey): array
    {
        foreach($configuration as $key => &$context) {

            if(!is_array($context)) {
                throw new \LogicException('Each configuration structure must have data of type array');
            }

            if(empty($context['field'])) {
                $context['field'] = TextField::new(self::FIELD_NAME);
            }

            if(!is_integer($context['mode'] ?? null)) {
                $context['mode'] = ModeEnum::READ->value | ModeEnum::WRITE->value;
            }

            if(empty($context['label'])) {
                $context['label'] = ucwords(preg_replace('/[._]/', " ", $key));
            }
            
            $context['field']->setLabel($context['label']);

        };

        return empty($metaKey) ? $configuration : ($configuration[$metaKey] ?? null);
    }
}