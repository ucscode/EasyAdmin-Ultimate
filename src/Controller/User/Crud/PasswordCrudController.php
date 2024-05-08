<?php

namespace App\Controller\User\Crud;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContext;

class PasswordCrudController extends ProfileCrudController
{
    protected string $actionLabel = 'Update Password';

    public function __construct(protected UserPasswordHasherInterface $userPasswordHasher)
    {

    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setPageTitle(Crud::PAGE_EDIT, 'Update Password')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('oldPassword', 'Current Password')
            ->setFormType(PasswordType::class)
            ->setFormTypeOptions([
                'mapped' => false,
                'constraints' => new UserPassword(null, 'The current password is not correct')
            ])
            ->setRequired(true)
        ;

        yield $this->passwordFieldFactory('newPassword', null)
            ->setFormTypeOption('constraints', [
                // new PasswordStrength(null, PasswordStrength::STRENGTH_WEAK),
                new Callback(function (string $value, ExecutionContext $context): void {
                    /**
                     * @var \Symfony\Component\Form\FormInterface
                     */
                    $form = $context->getRoot();

                    if($form->get('oldPassword')->getData() == $value) {
                        $context
                            ->buildViolation('New password must be different from current password')
                            ->atPath('newPassword')
                            ->addViolation()
                        ;
                    };
                })
            ]);

        yield $this->passwordFieldFactory('confirmPassword', null)
            ->setFormTypeOption('constraints', new Callback(function (string $value, ExecutionContext $context) {
                /**
                 * @var \Symfony\Component\Form\FormInterface
                 */
                $form = $context->getRoot();

                if($form->get('newPassword')->getData() !== $value) {
                    $context
                        ->buildViolation('Password fields must match')
                        ->atPath('confirmPassword')
                        ->addViolation()
                    ;
                }
            }));
    }

    /**
     * @param \App\Entity\User $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /**
         * @var \App\Entity\User
         */
        $user = $this->getUser();

        $formData = $this->getContext()->getRequest()->request->all('User');

        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $formData['newPassword']);

        $entityInstance->setPassword($hashedPassword);

        parent::updateEntity($entityManager, $entityInstance);
    }


    protected function passwordFieldFactory(string $name, $label = null): TextField
    {
        return TextField::new($name, $label)
            ->setFormType(PasswordType::class)
            ->setFormTypeOption('mapped', false)
            ->setRequired(true)
        ;
    }
}
