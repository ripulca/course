<?php

namespace App\Controller\Admin;

use App\Entity\Contains;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContainsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contains::class;
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
