<?php

namespace App\Controller\User\Crud;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class PasswordCrudController extends ProfileCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setPageTitle(Crud::PAGE_EDIT, 'Update Password')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('oldPassword')
            ->setFormType(PasswordType::class)
            ->setFormTypeOptions([
                'mapped' => false,
            ])
            ->setRequired(true)
        ;

        yield TextField::new('plainPassword')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'New Password'],
                'second_options' => ['label' => 'Confirm Password'],
                'invalid_message' => 'The password fields must match.',
                'mapped' => false,
            ])
            ->setRequired(true)
        ;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $form = $this->getContext()->getRequest()->request->get('User');
        $oldPassword = $form['oldPassword'];
        $plainPassword = $form['plainPassword'];

        // ... check the old password and update the password ...

        parent::updateEntity($entityManager, $entityInstance);
    }

}
