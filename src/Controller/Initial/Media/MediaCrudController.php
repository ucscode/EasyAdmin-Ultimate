<?php

namespace App\Controller\Initial\Media;

use App\Entity\Media;
use App\Form\Field\VichField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MediaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('embeddedFile.originalName', 'Original Name')
            ->setDisabled()
            ->hideWhenCreating()
        ;

        yield TextField::new('embeddedFile.name', 'Saved As')
            ->setDisabled()
            ->hideWhenCreating()
        ;

        yield TextField::new('embeddedFile.mimeType', 'Mime-Type')
            ->setDisabled()
            ->hideWhenCreating()
        ;

        yield IntegerField::new('embeddedFile.size', 'File Size')
            ->setDisabled()
            ->hideWhenCreating()
            ->setFormTypeOption('affix', [
                'append' => 'Byte'
            ])
        ;
        
        yield VichField::new('uploadedFile')

        ;
    }
}
