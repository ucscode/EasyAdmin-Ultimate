<?php

namespace App\Controller\Admin\Crud\User;

use App\Constants\FilePathConstants;
use App\Constants\RoleConstants;
use App\Controller\Admin\Abstracts\AbstractAdminCrudController;
use App\Controller\Admin\DashboardController;
use App\Controller\Initial\User\HierarchyController;
use App\Entity\User\User;
use App\Form\Extension\Affix\AffixTypeExtension;
use App\Repository\User\UserRepository;
use App\Security\PasswordStrengthEstimator;
use App\Service\AffiliationService;
use Carbon\Carbon;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Ucscode\KeyGenerator\KeyGenerator;

class UserCrudController extends AbstractAdminCrudController
{
    protected KeyGenerator $keyGenerator;

    public function __construct(
        protected AdminUrlGenerator $adminUrlGenerator,
        protected UserPasswordHasherInterface $userPasswordHasher,
        protected PasswordStrengthEstimator $passwordStrengthEstimator,
        protected AffiliationService $affiliationService
    ) {
        $this->keyGenerator = new KeyGenerator();
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        /**
         * @var ?User
         */
        $entity = $this->getContext()->getEntity()->getInstance();

        yield FormField::addTab('Basic Info', 'fas fa-info-circle')
            ->setHelp($this->getTabHelp())
            // Basic Tab
        ;

        yield TextField::new('uniqueId')
            ->setDisabled()
            ->onlyOnForms()
            ->hideWhenCreating()
        ;

        yield EmailField::new('email');

        yield BooleanField::new('isVerified', 'Verified')
            ->renderAsSwitch(false)
        ;

        yield TextField::new('username');

        yield $this->passwordFieldFactory($pageName);

        yield DateTimeField::new('registrationTime')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('lastSeen')
            ->hideOnForm()
            ->formatValue(function (DateTimeInterface $value) {
                return Carbon::instance($value)->diffForHumans();
            })
        ;

        yield FormField::addTab('Advance Info', 'fas fa-user-tie')
            ->setHelp($this->getTabHelp())
            # Advance Info Tab
        ;

        yield ImageField::new('avatar')
            ->setUploadDir(FilePathConstants::USER_IMAGE_UPLOAD_DIR)
            ->setBasePath(FilePathConstants::USER_IMAGE_BASE_PATH)
            ->setUploadedFileNamePattern(FilePathConstants::USER_IMAGE_UPLOAD_FILE_PATTERN)
        ;

        yield ChoiceField::new('roles')
            ->allowMultipleChoices()
            ->setChoices($this->getAllowedRoles())
        ;

        if($this->affiliationService->isEnabled()) {

            yield TextField::new('referral_link')
                ->setFormTypeOptions([
                    'mapped' => false,
                    'required' => false,
                    'data' => $this->affiliationService->getReferralLink($entity),
                    'affix' => [
                        'append' => AffixTypeExtension::CLIP_COPY_BUTTON
                    ]
                ])
                ->setHtmlAttribute('readonly', 'readonly')
                ->onlyOnForms()
                ->hideWhenCreating()
            ;

            $parentField = AssociationField::new('parent')
                ->setCrudController(self::class)
                // ->hideOnIndex()
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
    }

    public function configureActions(Actions $actions): Actions
    {
        $propertyAction = Action::new('property', 'Properties')
            ->linkToUrl(
                function (User $entity) {
                    return $this->adminUrlGenerator
                        ->setDashboard(DashboardController::class)
                        ->setController(PropertyCrudController::class)
                        ->setAction(Crud::PAGE_INDEX)
                        ->set('userId', $entity->getId())
                        ->setEntityId(null)
                        ->generateUrl()
                    ;
                }
            );
        ;

        $actions->add(Crud::PAGE_INDEX, $propertyAction);

        if($this->affiliationService->isEnabled()) {
            // create hierarchy action
            $hierarchyAction = Action::new('hierarchy', 'Hierarchy')
                ->linkToUrl(function (User $entity) {
                    return $this->adminUrlGenerator
                        ->setRoute(HierarchyController::ROUTE_NAME, [
                            'entityId' => $entity->getId(),
                        ])
                    ;
                })
            ;

            $actions->add(Crud::PAGE_INDEX, $hierarchyAction);
        }

        $actions->reorder(Crud::PAGE_INDEX, [
            Action::EDIT,
            'property'
        ]);

        return $actions;
    }

    public function createEntity(string $entityFqcn): User
    {
        /**
         * @var User
         */
        $entity = parent::createEntity($entityFqcn);

        $entity->setUniqueId($this->keyGenerator->generateKey(7));

        return $entity;
    }

    /**
     * @param User $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $hashedPassword = $this->userPasswordHasher->hashPassword($entityInstance, $entityInstance->getPassword());
        $entityInstance->setPassword($hashedPassword);

        /**
         * Create an persist properties here
         * Make your custom modification here
         */
        parent::persistEntity($entityManager, $entityInstance);
    }

    /**
     * @param \App\Entity\User\User $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $submittedPassword = $entityInstance->getPassword();
        $originalEntityData = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);

        $hashedPassword = empty($submittedPassword) ? $originalEntityData['password'] : $this->userPasswordHasher->hashPassword(
            $entityInstance,
            $submittedPassword
        );

        $entityInstance->setPassword($hashedPassword);

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
                ],
                'constraints' => [
                    new Callback(function (?string $password, ExecutionContextInterface $context): void {
                        if($password) {
                            $callable = $this->passwordStrengthEstimator->getConstraintArgument(
                                'password',
                                PasswordStrength::STRENGTH_MEDIUM,
                                'The new password is not strong enough'
                            );
                            call_user_func($callable, $password, $context);
                        }
                    })
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

    /**
     * Retrieves an array of allowed roles.
     *
     * The method returns a predefined list of roles mapped to display values.
     * If no specific roles are defined in the array, it defaults to returning all roles
     * starting with 'ROLE_' from RoleConstants::getChoices().
     *
     * @return array An associative array where the key is the display value and the value is the role constant.
     */
    protected function getAllowedRoles(): array
    {
        return [
            // 'Display Value' => RoleConstants::ROLE_ADMIN

        ] ?: RoleConstants::getChoices('ROLE_');
    }

    protected function getTabHelp(): string
    {
        /**
         * @var ?User
         */
        $entity = $this->getContext()->getEntity()->getInstance();

        return !$entity?->getEmail() ? '' : sprintf(
            "<div class='text-bg-light my-3 p-2 rounded'>
                @user &lt;%s&gt;
            </div>",
            $entity->getEmail(),
        );
    }
}
