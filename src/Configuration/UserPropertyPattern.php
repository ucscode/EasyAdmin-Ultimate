<?php

namespace App\Configuration;

use App\Component\Abstracts\AbstractPattern;
use App\Constants\ModeConstants;
use App\Utils\Stateless\CaseConverter;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPropertyPattern extends AbstractPattern
{    
    /**
     * The pattern defined here are merily examples
     * You should define pattenrs that suits your project
     */
    protected function buildPattern(): void
    {
        $this
            ->setPattern('firstName')

            ->setPattern('lastName')

            ->setPattern('about', [
                'field' => TextareaField::class,
                'label' => 'About GG'
            ])

            ->setPattern('balance', [
                'value' => 0,
                'field' => MoneyField::class,
                'configureField' => function(MoneyField $field) {
                    $field->setCurrency('USD');
                },
            ])
            
            ->setPattern('hasPremiumAccount', [
                'field' => BooleanField::class,
                'value' => false,
                'mode' => ModeConstants::NO_PERMISSION
            ])
        ;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'label' => null,
                'value' => null,
                'mode' => ModeConstants::READ|ModeConstants::WRITE,
                'field' => TextField::class,
                'configureField' => null,
            ]);
        
        $resolver
            ->setAllowedTypes('label', ['string', 'null'])
            ->setAllowedTypes('mode', 'integer')
            ->setAllowedTypes('field', 'string')
            ->setAllowedTypes('configureField', ['callable', 'null'])
        ;

        $resolver
            ->setNormalizer('label', function(Options $options, ?string $label) {
                return $label ?? ucwords(CaseConverter::toSentenceCase($options[self::OPTION_OFFSET]));
            })
            
            ->setNormalizer('field', function(Options $options, string $fieldFqcn) {
                if(!in_array(FieldInterface::class, \class_implements($fieldFqcn))) {
                    throw new InvalidOptionsException(sprintf(
                        'field %s must implement %s',
                        $fieldFqcn,
                        FieldInterface::class
                    ));
                };

                $field = $fieldFqcn::new('metaValue', $options['label']);
                
                if(is_callable($options['configureField'])) {
                    call_user_func($options['configureField'], $field);
                }

                return $field;
            })
        ;
    }
}