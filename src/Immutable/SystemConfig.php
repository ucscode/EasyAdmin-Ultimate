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
    const USER_IMAGE_UPLOAD_DIR = 'public/assets/images/users';
    const USER_IMAGE_BASE_PATH = 'assets/images/users';
    const USER_IMAGE_UPLOAD_FILE_PATTERN = '[contenthash]-[timestamp].[extension]';

    private static $metaValueName = 'metaValue';

    public static function getConfigurationStructure(?string $metaKey = null): ?array
    {
        $configuration = [
            'app.name' => 'User Synthetics',
            'app.logo' => [
                'value' => 'http://ucscode.com/common/images/origin.png',
                'field' => ImageField::new(self::$metaValueName)
                    ->setUploadDir('public/assets/images/system')
                    ->setBasePath('assets/images/system'),
            ],
            'app.slogan' => [
                'value' => 'Your premier destination for creating stunning and effective websites.',
                'field' => TextareaField::new(self::$metaValueName),
            ],
            'app.description' => [
                'value' => "Our comprehensive suite of services covers every aspect of website creation, from concept and design to development and launch. We work closely with our clients to understand their goals, audience, and brand identity, ensuring that every website we create reflects their vision and objectives.",
                'field' => TextareaField::new(self::$metaValueName),
            ],
            'office.email' => [
                'value' => 'office@example.com',
                'field' => EmailField::new(self::$metaValueName),
            ],
            'office.phone' => [
                'value' => '+1 212-555-0123',
                'field' => TelephoneField::new(self::$metaValueName),
            ],
            'office.address' => [
                'value' => "123 Main Street \nAnytown, CA 12345 \nUnited States",
                'field' => TextareaField::new(self::$metaValueName),
            ],
            'test.key' => [
                'value' => false,
                'field' => BooleanField::new(self::$metaValueName),
            ],
            'test.hidden' => [
                'value' => 'seen',
                'mode' => 0,
            ]
        ];

        return self::formatConfigurationStructure($configuration, $metaKey);
    }

    private static function formatConfigurationStructure(array &$configuration, string $metaKey): array
    {
        foreach($configuration as $key => &$context) {
            is_array($context) ?: $context = ['value' => $context];
            !empty($context['field']) ?: $context['field'] = TextField::new(self::$metaValueName);
            is_integer($context['mode'] ?? null) ?: $context['mode'] = ModeEnum::READ->value|ModeEnum::WRITE->value;
            !empty($context['label']) ?: $context['label'] = ucwords(str_replace('.', " ", $key));
            $context['field']->setLabel($context['label']);
        };
        return empty($metaKey) ? $configuration : ($configuration[$metaKey] ?? null);
    }
}