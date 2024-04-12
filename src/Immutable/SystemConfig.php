<?php

namespace App\Immutable;

use App\Enum\ModeEnum;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class SystemConfig
{
    const SYSTEM_CSS_FILE = 'resource/css/style.css';
    const SYSTEM_JS_FILE = 'resource/css/script.js';

    const USER_IMAGE_UPLOAD_DIR = 'public/resource/images/users';
    const USER_IMAGE_BASE_PATH = 'resource/images/users';
    const USER_IMAGE_UPLOAD_FILE_PATTERN = '[contenthash]-[timestamp].[extension]';

    private static $fieldProperty = 'metaValue';

    public static function getConfigurationStructure(?string $metaKey = null): ?array
    {
        /**
         * System Default Configurations
         * -----------------------------
         * 
         * $metaKey => (array) $context
         * 
         * If a new configuration is added to the list, you must run the following command to implement it:
         * 
         * $ php bin/console uss:initialize
         * 
         * @var array
         */
        $configuration = [

            'app.name' => 'User Synthetics',

            'app.logo' => [
                'value' => 'http://ucscode.com/common/images/origin.png',
                'field' => ImageField::new(self::$fieldProperty)
                    ->setUploadDir('public/assets/images/system')
                    ->setBasePath('assets/images/system'),
            ],

            'app.slogan' => [
                'value' => 'Your premier destination for creating stunning and effective websites.',
                'field' => TextareaField::new(self::$fieldProperty),
            ],

            'app.description' => [
                'value' => "Our comprehensive suite of services covers every aspect of website creation, from concept and design to development and launch. We work closely with our clients to understand their goals, audience, and brand identity, ensuring that every website we create reflects their vision and objectives.",
                'field' => TextareaField::new(self::$fieldProperty),
            ],

            'office.email' => [
                'value' => 'office@example.com',
                'field' => EmailField::new(self::$fieldProperty),
            ],

            'office.phone' => [
                'value' => '+1 212-555-0123',
                'field' => TelephoneField::new(self::$fieldProperty),
            ],

            'office.address' => [
                'value' => "123 Main Street \nAnytown, CA 12345 \nUnited States",
                'field' => TextareaField::new(self::$fieldProperty),
            ],

            'test.key' => [
                'value' => false,
                'field' => BooleanField::new(self::$fieldProperty),
            ],

            'test.hidden' => [
                'value' => 'seen',
                'mode' => 0,
            ]

        ];

        return self::expandEachConfigurationContext($configuration, $metaKey);
    }

    private static function expandEachConfigurationContext(array &$configuration, ?string $metaKey): array
    {
        foreach($configuration as $key => &$context) {

            // Ensure that context is an array with "value" offset
            is_array($context) ?: $context = ['value' => $context];

            // Ensure that context has a "field" offset (default: TextField)
            !empty($context['field']) ?: $context['field'] = TextField::new(self::$fieldProperty);

            // Ensure that context has "mode" offset (default: 4|2)
            is_integer($context['mode'] ?? null) ?: $context['mode'] = ModeEnum::READ->value|ModeEnum::WRITE->value;

            // Ensure that context has a label
            !empty($context['label']) ?: $context['label'] = ucwords(str_replace('.', " ", $key));

            // Update the label of the field
            $context['field']->setLabel($context['label']);

        };

        return empty($metaKey) ? $configuration : ($configuration[$metaKey] ?? null);
    }
}