<?php

namespace App\Controller\Admin;

use App\Entity\Kitten;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\HttpFoundation\RequestStack;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use App\Enum\StatusType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class KittenCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Kitten::class;
    }
    public function __construct(private RequestStack $requestStack) {}
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request->files->has('Kitten')) {
            $image = $request->files->get('Kitten')['imageFile'] ?? null;
            if ($image) {
                // насильно сообщаем Doctrine, что сущность изменилась
                $entityManager->persist($entityInstance);
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    
    public function configureFields(string $pageName): iterable
    {
        $kitten = $this->getContext()?->getEntity()?->getInstance();

        $breedId = $kitten?->getBreed()?->getId();
        return [
            TextField::new('name'),
            AssociationField::new('breed'),
            AssociationField::new('gender'),
            AssociationField::new('color'),
            IntegerField::new('price'),
            AssociationField::new('kittenStatus'),
            AssociationField::new('litter'),
            ImageField::new('imageLink')
                ->setBasePath('/img/cats')
                ->setUploadDir('public/img/cats')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->onlyOnIndex(),
            TextField::new('imageFile')
                ->setFormType(VichImageType::class)
                ->setLabel('Image')
                ->onlyOnForms(),
        ];
    }
    
}
