<?php

namespace App\Controller\Admin;

use App\Entity\GuestRequest;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GuestRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GuestRequest::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT)
            ->add(Action::INDEX, Action::DETAIL);// Запрещает добавление новых записей
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('name'),
            TextField::new('email'),
            TextField::new('phone'),
            TextEditorField::new('message'), // слишком длинное — только в detail
            DateField::new('requestDate'),
        ];
    }
}
