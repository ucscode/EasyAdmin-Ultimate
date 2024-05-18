<?php

namespace App\Config;

use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

/**
 * Define user property configuration that will be used across the application
 * 
 * Configuration Options:
 * 
 * - label (optional): The label to render to user
 * 
 * - value (optional): The default value to set for the property
 * 
 * - mode (option): The permission to assign for admin accessibility
 * 
 * - field (optional): An easyadmin Field Interface class Name that will be used in editing the property
 * 
 * - configure_field (optional): The callable (closure) to configure the field instance field type
 * 
 *      - If the option accepts multiple parameters, use an array to define each parameters instead
 */
return static function(): array {

    return [

        'firstName' => [],

        'lastName' => [],

        'about' => [
            'field' => TextareaField::class,
            'label' => 'About '
        ],

        'balance' => [
            'field' => MoneyField::class,
            'value' => 0,
            'configure_field' => function(MoneyField $field) {
                $field->setCurrency('USD');
            },
        ],

        'hasPremiumAccount' => [
            'field' => BooleanField::class,
            'value' => false,
            'mode' => 4
        ]

    ];

};