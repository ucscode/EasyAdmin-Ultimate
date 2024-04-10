<?php

namespace App\Immutable;

use App\Enum\ModeEnum;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class SystemConfig
{
    const USER_IMAGE_UPLOAD_DIR = 'public/assets/images/users';
    const USER_IMAGE_BASE_PATH = 'assets/images/users';
    const USER_IMAGE_UPLOAD_FILE_PATTERN = '[contenthash]-[timestamp].[extension]';

    const ADMIN_CONFIG_STRUCTURE = [

        'app.name' => [
            'value' => 'User Synthetics',
            'mode' => ModeEnum::READ_WRITE,
        ],
        
        'app.logo' => [
            'value' => 'http://ucscode.com/common/images/origin.png',
            'field' => ImageField::class,
            'image' => [
                'upload_dir' => 'public/assets/images/system',
                'assets/images/system',
            ],
        ],

        'app.slogan' => [
            'value' => 'Commercial web framework for building professional application',
        ],

        'app.description' => [
            'value' => 'Describe your website briefly! This may be visible to client that visit your page',
            'field' => TextareaField::class,
        ],

        'office.email' => [
            'value' => 'webmail@example.com',
            'field' => EmailField::class,
        ],

        'office.phone' => [
            'value' => '123455',
            'field' => TelephoneField::class,
        ],

        'office.address' => [
            'value' => '123 Main Street Cityville, Stateville UK, 12345',
            'field' => TextareaField::class,
        ],
    ];
}