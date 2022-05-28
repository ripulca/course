<?php

namespace App\Controller\Admin;

use App\Entity\Provides;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProvidesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Provides::class;
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
