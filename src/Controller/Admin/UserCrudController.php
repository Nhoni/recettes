<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')

            ->setPageTitle('index', 'Liste des utilisateurs')
            ->setPaginatorPageSize(10);
    }

    public function configureFields(string $pageName): iterable
    {

        yield IdField::new('id')
            ->hideOnForm();
        yield TextField::new('fullName');
        yield TextField::new('pseudo');
        yield TextField::new('email')
            ->hideOnForm();
        yield ArrayField::new('roles')
            ->hideOnForm();
        yield DateTimeField::new('createdAt')
            ->setFormTypeOption('disabled', 'disabled')
            ->hideOnForm();
    }
}
