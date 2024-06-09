<?php

namespace App\Form\Extension\Affix;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffixTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [
            FormType::class
        ];
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars[AffixResolver::AFFIX_KEY] = $options[AffixResolver::AFFIX_KEY];
    }

    /**
     * Add extra option to input field which allows dynamic side icon, text or button: Example
     * 
     * affix:
     *  - prepend
     *      - type: button
     *      - value: 'button value'
     *  - append
     *      - type: icon
     *      - value: 'fa fa-name'
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $affixResolver = new AffixResolver($resolver);
        $affixResolver->resolveAffix($resolver);
    }
}