<?php

namespace App\Controller\Initial\Media;

use App\Controller\Initial\Abstracts\AbstractInitialCrudController;
use App\Entity\Media;
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

        if($entity) {
            $fileUrl = sprintf(
                '%s%s',
                $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost(),
                $this->uploaderHelper->asset($entity, 'uploadedFile'),
            );

            $fileUrlField = TextField::new(self::FIELD_FILE_URL)
                ->hideWhenCreating()
                ->hideOnIndex()
                ->setFormTypeOptions($this->relativeFieldOptions() + [
                    'mapped' => false,
                    'data' => $fileUrl,
                ])
                ->setDisabled()
            ;
            
            if(in_array($entity->getMimeParts(0), [Media::TYPE_IMAGE, Media::TYPE_VIDEO])) {
                $fileUrlField->setHelp(
                    sprintf(
                        "<a href='%s' class='text-capitalize' data-glightbox>
                            <i class='fa fa-image'></i> Preview %s
                        </a>", 
                        $fileUrl,
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

    protected function relativeFieldOptions(array $replacements = []): array
    {
        return array_replace_recursive([
            'affix' => [
                'append' => [
                    'type' => 'button',
                    'value' => [
                        'icon' => 'fas fa-copy',
                        'attributes' => [
                            'title' => 'copy',
                            'data-media-field-copier' => '',
                        ],
                    ],
                ],
            ],
        ], $replacements);
    }
}
