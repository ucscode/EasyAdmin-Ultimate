<?php

namespace App\Form\Auth;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', TextType::class, [
                'attr' => [
                    'autofocus' => true,
                    'autocomplete' => 'username',
                ]
            ])
            ->add('_password', PasswordType::class, [
                'attr' => [
                    'autocomplete' => "current-password"
                ],
            ])
            ->add('_remember_me', CheckboxType::class, [
                'required' => false
            ])
            ->add('_submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
