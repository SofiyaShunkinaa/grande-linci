<?php

namespace App\Controller\Admin;

use App\Entity\Cat;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class CatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cat::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextAreaField::new('description'),
            TextAreaField::new('descriptionRu'),
            TextAreaField::new('descriptionLv'),
            AssociationField::new('breed'),
            AssociationField::new('gender'),
            ImageField::new('imageLink')
                ->setBasePath('/images/cats')
                ->setUploadDir('public/images/cats')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->onlyOnIndex(),
            TextField::new('imageFile')
                ->setFormType(VichImageType::class)
                ->setLabel('Image')
                ->onlyOnForms(),
        ];
    }
    
}
