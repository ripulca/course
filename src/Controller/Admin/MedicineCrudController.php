<?php

namespace App\Controller\Admin;

use App\Entity\Medicine;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MedicineCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Medicine::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
