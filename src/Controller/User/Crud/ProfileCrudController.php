<?php

namespace App\Controller\User\Crud;

use App\Constants\FilePathConstants;
use App\Controller\User\Abstracts\AbstractUserCrudController;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProfileCrudController extends AbstractUserCrudController
{
    protected string $actionLabel = 'Update Profile';

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_EDIT, 'My Profile')
            ->overrideTemplate('crud/edit', 'user/profile.html.twig')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        yield ImageField::new('avatar')
            ->setUploadDir(FilePathConstants::USER_IMAGE_UPLOAD_DIR)
            ->setBasePath(FilePathConstants::USER_IMAGE_BASE_PATH)
        ;

        yield TextField::new('username')
            ->setDisabled(!empty($user->getUsername()));

        yield EmailField::new('email');
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)

            ->disable(Action::INDEX, Action::NEW, Action::DELETE, Action::BATCH_DELETE, Action::DETAIL)

            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN)

            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, function (Action $action) {
                return $action
                    ->setLabel($this->actionLabel)
                ;
            })

        ;
    }
}