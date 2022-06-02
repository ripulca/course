<?php

namespace App\Controller\Admin;

use App\Entity\Provides;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProvidesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Provides::class;
    }

    // public function configureCrud(Crud $crud): Crud{
    //     return $crud
    //         ->setEntityLabelInSingular('Conference Comment')
    //         ->setEntityLabelInPlural('Conference Comments')
    //         ->setSearchFields(['author', 'text', 'email'])
    //         ->setDefaultSort(['createdAt' => 'DESC'])
    //     ;
    // }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('provider'),
            AssociationField::new('medicine'),
            IntegerField::new('amount')
        ];
    }

}
