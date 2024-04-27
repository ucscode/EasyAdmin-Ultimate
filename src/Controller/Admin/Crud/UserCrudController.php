<?php

namespace App\Controller\Admin\Crud;

use App\Constants\FilePathConstants;
use App\Controller\Admin\Abstract\AbstractAdminCrudController;
use App\Controller\Admin\DashboardController;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\RoleUtils;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
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
            ->setDisabled()
            ->hideWhenCreating()
        ;

        yield ImageField::new('avatar')
            ->setUploadDir(FilePathConstants::USER_IMAGE_UPLOAD_DIR)
            ->setBasePath(FilePathConstants::USER_IMAGE_BASE_PATH)
            ->setUploadedFileNamePattern(FilePathConstants::USER_IMAGE_UPLOAD_FILE_PATTERN)
        ;

        yield EmailField::new('email');

        yield TextField::new('username');

        yield $this->passwordFieldFactory($pageName);

        yield DateTimeField::new('registrationTime')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('lastSeen')
            ->hideOnForm()
            ->formatValue(function (DateTimeInterface $value) {
                return $this->primaryTaskService->relativeTime($value, true);
            })
        ;

        yield ChoiceField::new('roles')
            ->allowMultipleChoices()
            ->setChoices($this->getAllowedRoles())
        ;

        yield BooleanField::new('isVerified', 'verified')
            ->renderAsSwitch(false)
        ;

        $parentField = AssociationField::new('parent')
            ->hideOnIndex()
        ;

        /**
         * Filter parent associates to prevent user from selecting oneself as parent
         */
        if(in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_NEW])) {

            $parentField->setFormTypeOption('query_builder', function (UserRepository $userRepository): QueryBuilder {
                /**
                 * @var User
                 */
                $entity = $this->getContext()->getEntity()->getInstance();
                $queryBuilder = $userRepository->createQueryBuilder('u');

                if($entity?->getId()) {
                    $queryBuilder
                        ->andWhere('u.id != :currentUserId')
                        ->setParameter('currentUserId', $entity->getId());
                };

                return $queryBuilder;
            });
        }

        yield $parentField;
    }

    public function configureActions(Actions $actions): Actions
    {
        $userPropertyAction = Action::new('userProperty', 'Properties')
            ->linkToUrl(
                function (User $entity) {
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

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashEntityPassword($entityInstance, $entityInstance->getPassword());

        /**
         * Make your custom modification here
         */
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $submittedPassword = trim($entityInstance->getPassword() ?? '');

        $originalEntityData = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);

        /**
         * @var User $entityInstance
         */
        $entityInstance->setPassword($originalEntityData['password'], false); // restore original password

        empty($submittedPassword) ?: $this->hashEntityPassword($entityInstance, $submittedPassword);

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

        return $allowedRoles ?: RoleUtils::getChoices('ROLE_');
    }

    private function hashEntityPassword(User $entity, string $plainPassword): void
    {
        $entity->setPassword(
            $this->userPasswordHasher->hashPassword(
                $entity,
                $plainPassword
            )
        );
    }
}
