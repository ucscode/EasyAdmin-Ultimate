<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Abstract\AbstractAdminCrudController;
use App\Entity\User;
use App\Immutable\SystemConfig;
use App\Immutable\UserRole;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractAdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('uniqueId')
            ->setDisabled();

        yield ImageField::new('avatar')
            ->setUploadDir(SystemConfig::USER_IMAGE_UPLOAD_DIR)
            ->setBasePath(SystemConfig::USER_IMAGE_BASE_PATH)
            ->setUploadedFileNamePattern(SystemConfig::USER_IMAGE_UPLOAD_FILE_PATTERN)
        ;
        
        yield EmailField::new('email');

        yield TextField::new('username');

        yield $this->passwordFieldFactory($pageName);

        yield DateTimeField::new('registrationTime');

        yield DateTimeField::new('lastSeen')->hideOnForm();

        yield ChoiceField::new('roles')
            ->allowMultipleChoices()
            ->setChoices($this->getAllowedRoles())
        ;

        $parentField = AssociationField::new('parent')
            ->hideOnIndex();
        
        if($this->getUser()) {
            $parentField->setFormTypeOption('query_builder', function (UserRepository $userRepository) {
                return $userRepository->createQueryBuilder('u')
                    ->andWhere('u.id != :currentUserId')
                    ->setParameter('currentUserId', $this->getUser()->getId());
            });
        }

        yield $parentField;
    }

    public function configureActions(Actions $actions): Actions
    {
        $userPropertyAction = Action::new('userProperty', 'Properties')
            ->linkToUrl(
                function(User $entity) {
                    return $this->adminUrlGenerator
                        ->setDashboard(DashboardController::class)
                        ->setController(UserPropertyCrudController::class)
                        ->setAction(Crud::PAGE_INDEX)
                        ->set('userId', $entity->getId())
                        ->generateUrl()
                    ;
                }
            );
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, $userPropertyAction)
        ;
    }

    public function createEntity(string $entityFqcn): User
    {
        /**
         * Modify the entity instance or create associates entities
         * 
         * Example: Your can add mandatory `UserProperty` to the entity instance
         * 
         * @var User
         */
        $entity = parent::createEntity($entityFqcn);

        return $entity;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $originalEntityData = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);
        $plainPassword = trim($entityInstance->getPassword() ?? '');

        !empty($plainPassword) ?: $entityInstance->setPassword($originalEntityData['password'], false); // restore original password

        /**
         * Make your custom modification here
         */

        parent::updateEntity($entityManager, $entityInstance); // Flush & Persist
    }

    protected function passwordFieldFactory(string $pageName): TextField
    {
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

        return $passwordField;
    }

    protected function getAllowedRoles(): array
    {
        /**
         * Get the array of allowed user roles.
         *
         * If the array is empty, all roles will be returned.
         *
         * @var array
         */

        $allowedRoles = [
            // 'Display Value' => UserRole::ROLE_ADMIN
        ];

        return $allowedRoles ?: UserRole::all(true);
    }
}
