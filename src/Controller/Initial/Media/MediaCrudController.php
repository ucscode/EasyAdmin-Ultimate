<?php

namespace App\Controller\Initial\Media;

use App\Entity\Media;
use App\Form\Field\VichField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class MediaCrudController extends AbstractCrudController
{
    protected const ACTION_FILE_URL = 'fileUrl';

    public function __construct(protected UploaderHelper $uploaderHelper)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplates([
                'crud/index' => 'initial/media/index.html.twig',
                'crud/new' => 'initial/media/new.html.twig',
                'crud/edit' => 'initial/media/edit.html.twig',
            ])
        ;
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

        yield DateField::new('updatedAt')
            ->setDisabled()
            ->hideWhenCreating()
        ;

        yield VichField::new('uploadedFile')
            
        ;
        
        yield TextField::new('fileUrl')
            ->setFormTypeOptions([
                'mapped' => false,
                'data' => 4,
                'attr' => [
                    'id' => 'media-file-url'
                ],
                'affix' => [
                    'append' => [
                        'type' => 'button',
                        'value' => [
                            'label' => 'copy',
                            'attributes' => [
                                'data-file-copy' => '#Media_fileUrl'
                            ]
                        ]
                    ]
                ]
            ])
            ->setDisabled()
            ->formatValue(function($entity) {
                return 12; //$this->uploaderHelper->asset($entity, 'name');
            });

    }

    public function configureActions(Actions $actions): Actions
    {
        $copyUrlAction = Action::new(self::ACTION_FILE_URL, 'Copy File Url')
            ->setHtmlAttributes(['data-file-copy' => ''])
            ->linkToUrl(function(Media $entity) {
                return $this->uploaderHelper->asset($entity, 'uploadedFile');
            })
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, $copyUrlAction)
        ;
    }
}
