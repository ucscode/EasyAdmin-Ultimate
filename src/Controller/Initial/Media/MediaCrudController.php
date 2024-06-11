<?php

namespace App\Controller\Initial\Media;

use App\Entity\Media;
use App\Form\Field\VichField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class MediaCrudController extends AbstractCrudController
{
    protected const FIELD_FILE_URL = 'fileUrl';

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
        yield FormField::addColumn('col-xl-6 col-xxl-5');
        yield FormField::addFieldset();

        yield VichField::new('uploadedFile', $pageName === Crud::PAGE_NEW ? 'Select File' : 'Uploaded File');
        
        $entity = $this->getContext()->getEntity()->getInstance();

        if($entity) {
            yield TextField::new(self::FIELD_FILE_URL)
                ->hideWhenCreating()
                ->hideOnIndex()
                ->setFormTypeOptions($this->relativeFieldOptions() + [
                    'mapped' => false,
                    'data' => $this->uploaderHelper->asset($entity, 'uploadedFile'),
                ])
            ;
        }

        if($pageName !== Crud::PAGE_NEW) {
            
            yield FormField::addColumn('col-xl-6 col-xxl-5');
            yield FormField::addFieldset('File Info', 'fa fa-info-circle');

            yield TextField::new('embeddedFile.originalName', 'Original Name')
                ->setFormTypeOptions($this->relativeFieldOptions())
            ;

            yield TextField::new('embeddedFile.name', 'Saved As')
                ->setFormTypeOptions($this->relativeFieldOptions())
            ;

            yield TextField::new('embeddedFile.mimeType', 'Mime-Type')
                ->setFormTypeOptions($this->relativeFieldOptions())
            ;

            yield IntegerField::new('embeddedFile.size', 'File Size')
                ->setFormTypeOptions($this->relativeFieldOptions())
            ;

            yield DateField::new('updatedAt')
                ->setDisabled()
            ;
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        $copyUrlAction = Action::new(self::FIELD_FILE_URL, 'Copy File Url')
            ->setHtmlAttributes(['data-file-copy' => ''])
            ->linkToUrl(function(Media $entity) {
                return $this->uploaderHelper->asset($entity, 'uploadedFile');
            })
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, $copyUrlAction)
        ;
    }

    protected function relativeFieldOptions(array $attributes = []): array
    {
        return [
            'affix' => [
                'append' => [
                    'type' => 'button',
                    'value' => [
                        'icon' => 'fas fa-copy',
                        'attributes' => [
                            'title' => 'copy',
                            'data-media-field-copier' => '',
                        ] + $attributes,
                    ],
                ],
            ],
            'attr' => [
                'disabled' => 'disabled',
            ]
        ];
    }
}
