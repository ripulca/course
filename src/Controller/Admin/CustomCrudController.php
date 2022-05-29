<?php

namespace App\Controller\Admin;

use App\Entity\Custom;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CustomCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Custom::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('customer'),
            AssociationField::new('courier'),
            AssociationField::new('doctor'),
            MoneyField::new('price')->setCurrency('RUB'),
            DateTimeField::new('payment_date'),
            DateTimeField::new('complete_date'),
            TextEditorField::new('address'),
            BooleanField::new('is_in_cart')
        ];
    }
}
