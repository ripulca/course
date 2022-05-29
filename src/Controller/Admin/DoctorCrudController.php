<?php

namespace App\Controller\Admin;

use App\Entity\Doctor;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DoctorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Doctor::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('user_profile'),
            AssociationField::new('hospital'),
            TextEditorField::new('specialization'),
            TextEditorField::new('post'),
        ];
    }
}
