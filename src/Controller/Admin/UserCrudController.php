<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Immutable\SystemConfig;
use App\Immutable\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('uniqueId')
            ->hideOnForm();

        yield ImageField::new('avatar')
            ->setUploadDir(SystemConfig::USER_IMAGE_UPLOAD_DIR)
            ->setBasePath(SystemConfig::USER_IMAGE_BASE_PATH)
            ->setUploadedFileNamePattern(SystemConfig::USER_IMAGE_UPLOAD_FILE_PATTERN)
        ;
        
        yield EmailField::new('email');

        yield TextField::new('username');

        $passwordField = TextField::new('password')
            ->onlyOnForms()
            ->setFormTypeOptions([
                'required' => $pageName == Crud::PAGE_NEW,
                'attr' => [
                    'value' => ''
                ]
            ])
        ;

        if($pageName === Crud::PAGE_EDIT) {
            $passwordField
                ->setHelp(sprintf(
                    "<i class='%s'></i> %s", 
                    'fa fa-info-circle', 
                    'Leave blank to preserve current password'
                ));
        }

        yield $passwordField;

        yield DateTimeField::new('registrationTime');

        yield DateTimeField::new('lastSeen')->hideOnForm();

        yield ChoiceField::new('roles')
            ->allowMultipleChoices()
            ->setChoices(UserRole::all(true));
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $originalEntityData = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);
        $password = trim($entityInstance->getPassword() ?? '');
        !empty($password) ?: $entityInstance->setPassword($originalEntityData['password'], false);
        
        parent::updateEntity($entityManager, $entityInstance);
    }
}
