<?php

namespace App\Controller\Admin\Crud\Product;

use App\Entity\Product\Review;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ReviewCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Review::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('user')
            ->autocomplete()
        ;

        yield AssociationField::new('product')
            ->autocomplete()
        ;

        yield ChoiceField::new('rating')
            ->setChoices(array_combine(
                array_map(fn ($val) => $val . ' star', range(1, 5)),
                range(1, 5)
            ))
        ;

        yield TextareaField::new('content');

        yield ChoiceField::new('status')
            ->setChoices([
                'Approved' => 'approved',
                'Declined' => 'declined'
            ])
        ;
    }
}
