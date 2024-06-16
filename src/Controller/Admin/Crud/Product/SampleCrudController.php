<?php

namespace App\Controller\Admin\Crud\Product;

use App\Entity\Product\Sample;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SampleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sample::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return array_merge(
            [
                AssociationField::new('image')
                    ->autocomplete()
                    ->setQueryBuilder(function(QueryBuilder $builder) {
                        // allow only image files
                        return $builder->andWhere("entity.embeddedFile.mimeType LIKE 'image/%'");
                    })
                ,
            ],
            parent::configureFields($pageName),
        );
    }
}
