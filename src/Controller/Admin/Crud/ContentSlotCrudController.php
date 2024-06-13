<?php

namespace App\Controller\Admin\Crud;

use App\Configuration\Factory\ContentSlotPattern;
use App\Constants\SlotConstants;
use App\Controller\Admin\Abstracts\AbstractAdminCrudController;
use App\Entity\ContentSlot;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContentSlotCrudController extends AbstractAdminCrudController
{
    protected string $title;
    protected array $slotChoices;

    public function __construct(protected ContentSlotPattern $contentSlotPattern)
    {
        $this->slotChoices =  SlotConstants::getChoices('SLOT_', null, function (string $value) {
            $label = preg_replace(sprintf('/^%s/', preg_quote('SLOT_', '/')), '', $value);
            return str_replace('_', ' ', $label);
        });
    }

    public static function getEntityFqcn(): string
    {
        return ContentSlot::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular("Slot")
            ->setEntityLabelInPlural("Slots")
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title')
            ->setHelp('Define a title to remember the code')
        ;

        yield ChoiceField::new('slots', 'Position')
            ->setFormTypeOption('choices', array_flip($this->slotChoices))
            ->allowMultipleChoices()
            ->setHelp('Where in a page should the code be placed?')
            ->onlyOnForms()
        ;

        yield ChoiceField::new('targets')
            ->setFormTypeOption('choices', $this->getContentSlotChoices())
            ->allowMultipleChoices()
            ->setHelp('In what panel should this code be available?')
            ->onlyOnForms()
        ;

        yield BooleanField::new('enabled');

        yield NumberField::new('sort', 'Order')
            ->setHelp('In what order should the code be arranged')
        ;

        yield CodeEditorField::new('content', 'HTML Code')
            ->setLanguage('xml')
            ->setNumOfRows(20)
        ;
    }

    protected function getContentSlotChoices(): array
    {
        $choices = [];

        foreach($this->contentSlotPattern->getPatterns() as $pattern) {
            $choices[$pattern->get('title')] = $pattern->get(ContentSlotPattern::ACCESS_KEY);
        }

        return $choices;
    }
}
