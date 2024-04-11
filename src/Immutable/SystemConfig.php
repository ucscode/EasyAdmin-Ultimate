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

    public static function getConfigurationStructure(): array
    {
        $name = 'metaValue';

        $configuration = [
            'app.name' => 'User Synthetics',
            'app.logo' => [
                'value' => 'http://ucscode.com/common/images/origin.png',
                'field' => ImageField::new($name)
                    ->setUploadDir('public/assets/images/system')
                    ->setBasePath('assets/images/system'),
            ],
            'app.slogan' => [
                'value' => 'Your premier destination for creating stunning and effective websites.',
                'field' => TextareaField::new($name),
            ],
            'app.description' => [
                'value' => "Our comprehensive suite of services covers every aspect of website creation, from concept and design to development and launch. We work closely with our clients to understand their goals, audience, and brand identity, ensuring that every website we create reflects their vision and objectives.",
                'field' => TextareaField::new($name),
            ],
            'office.email' => [
                'value' => 'office@example.com',
                'field' => EmailField::new($name),
            ],
            'office.phone' => [
                'value' => '+1 212-555-0123',
                'field' => TelephoneField::new($name),
            ],
            'office.address' => [
                'value' => "123 Main Street \nAnytown, CA 12345 \nUnited States",
                'field' => TextareaField::new($name),
            ],
            'test.key' => [
                'value' => false,
                'field' => BooleanField::class,
            ],
            'test.hidden' => [
                'value' => 'seen',
                'mode' => 0,
            ]
        ];

        array_walk($configuration, function(&$context, $key) use ($name) {
            is_array($context) ?: $context = ['value' => $context];
            !empty($context['field']) ?: $context['field'] = TextField::new($name);
            is_integer($context['mode'] ?? null) ?: $context['mode'] = ModeEnum::READ->value|ModeEnum::WRITE->value;
        });

        return $configuration;
    }
}