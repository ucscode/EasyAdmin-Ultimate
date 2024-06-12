<?php

namespace App\Controller\Initial\Media;

use App\Controller\Initial\Abstracts\AbstractInitialCrudController;
use App\Entity\Media;
use App\Form\Extension\Affix\AffixTypeExtension;
use App\Form\Field\VichField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class MediaCrudController extends AbstractInitialCrudController
{
    protected const FIELD_FILE_URL = 'fileUrl';

    public function __construct(protected UploaderHelper $uploaderHelper, protected RequestStack $requestStack)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addColumn('col-xl-6 col-xxl-5');
        yield FormField::addFieldset();

        yield VichField::new('uploadedFile', $pageName === Crud::PAGE_NEW ? 'Select File' : 'Uploaded File')
            ->hideOnIndex()    
        ;
        
        /**
         * @var ?Media
         */
        $entity = $this->getContext()->getEntity()->getInstance();

        if($pageName === Crud::PAGE_EDIT && $entity) {
            $fileUrlField = TextField::new(self::FIELD_FILE_URL)
                ->hideWhenCreating()
                ->hideOnIndex()
                ->setFormTypeOptions($this->relativeFieldOptions() + [
                    'mapped' => false,
                    'data' => $this->getFileUrl($entity),
                ])
                ->setDisabled()
            ;
            
            if(in_array($entity->getMimeParts(0), [Media::TYPE_IMAGE, Media::TYPE_VIDEO])) {
                $fileUrlField->setHelp(
                    sprintf(
                        "<a href='%s' class='text-capitalize' data-glightbox>
                            <i class='fa fa-image'></i> Preview %s
                        </a>", 
                        $this->getFileUrl($entity),
                        $entity->getMimeParts(0)
                    )
                );
            }

            yield $fileUrlField;
        }

        if($pageName !== Crud::PAGE_NEW) {
            
            yield FormField::addColumn('col-xl-6 col-xxl-5');
            yield FormField::addFieldset('File Info', 'fa fa-info-circle');

            yield TextField::new('embeddedFile.originalName', 'Original Name')
                ->setFormTypeOptions($this->relativeFieldOptions())
                ->setDisabled()
            ;

            yield TextField::new('embeddedFile.name', 'Saved As')
                ->setFormTypeOptions($this->relativeFieldOptions())
                ->setDisabled()
            ;

            yield TextField::new('embeddedFile.mimeType', 'Mime-Type')
                ->setFormTypeOptions($this->relativeFieldOptions([
                    'affix' => [
                        'prepend' => [
                            'type' => 'icon',
                            'value' => 'fas fa-layer-group',
                        ],
                        'append' => null,
                    ]
                ]))
                ->setDisabled()
            ;

            if($entity) {
                yield IntegerField::new('embeddedFile.size', 'File Size (Bytes)')
                    ->setFormTypeOptions($this->relativeFieldOptions([
                        'affix' => [
                            'prepend' => [
                                'type' => 'icon',
                                'value' => 'fas fa-expand',
                            ],
                            'append' => null
                        ]
                    ]))
                    ->setDisabled()
                ;
            }

            yield DateField::new('updatedAt', 'Upload Date')
                ->setFormTypeOptions($this->relativeFieldOptions([
                    'affix' => [
                        'prepend' => [
                            'type' => 'icon',
                            'value' => 'fas fa-calendar'
                        ],
                        'append' => null,
                    ]
                ]))
                ->setRequired(false)
                ->setDisabled()
            ;
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        $copyAction = Action::new(self::FIELD_FILE_URL, 'Copy Link')
            ->linkToUrl(function(Media $entity) {
                return $this->getFileUrl($entity);
            })
            ->setHtmlAttributes([
                'data-media-index-clip' => ''
            ])
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, $copyAction)
            ->reorder(Crud::PAGE_INDEX, [
                Action::EDIT,
                self::FIELD_FILE_URL,
            ])
        ;
    }

    protected function relativeFieldOptions(array $replacements = []): array
    {
        return array_replace_recursive([
            'affix' => [
                'append' => AffixTypeExtension::CLIP_COPY_BUTTON,
            ],
        ], $replacements);
    }

    protected function getFileUrl(Media $entity): string
    {
        return sprintf(
            '%s%s',
            $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost(),
            $this->uploaderHelper->asset($entity, 'uploadedFile'),
        );
    }
}
