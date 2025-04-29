<?php

namespace App\Controller\Admin;

use App\Entity\Kitten;
use App\Repository\ColorRepository;
use App\Repository\LitterRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
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
    public function __construct(
        private RequestStack $requestStack,
        private LitterRepository $litterRepository,
        private ColorRepository $colorRepository,
    ) {}
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
        /** @var Kitten|null $kitten */
        $kitten = $this->getContext()?->getEntity()?->getInstance();
        $selectedBreed = $kitten?->getBreed();

        return [
            TextField::new('name'),
            AssociationField::new('breed'),

            AssociationField::new('gender'),

            // окрас, фильтруется по породе
            AssociationField::new('color')
                ->setFormTypeOptions([
                    'query_builder' => function (ColorRepository $repo) use ($selectedBreed) {
                        $qb = $repo->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');

                        if ($selectedBreed) {
                            $qb->where('c.breed = :breed')
                                ->setParameter('breed', $selectedBreed);
                        }

                        return $qb;
                    },
                ]),

            IntegerField::new('price'),
            AssociationField::new('kittenStatus'),

            // помёт, фильтруется по породе
            AssociationField::new('litter')
                ->setFormTypeOptions([
                    'query_builder' => function (LitterRepository $repo) use ($selectedBreed) {
                        $qb = $repo->createQueryBuilder('l')
                            ->orderBy('l.name', 'ASC');

                        if ($selectedBreed) {
                            $qb->where('l.breed = :breed')
                                ->setParameter('breed', $selectedBreed);
                        }

                        return $qb;
                    },
                ]),

            ImageField::new('imageLink')
                ->setBasePath('/img/cats')
                ->setUploadDir('public/img/cats')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->onlyOnIndex(),

            TextField::new('imageFile')
                ->setFormType(VichImageType::class)
                ->setLabel('Image')
                ->onlyOnForms(),

            // скрытое поле с JSON данными
            TextareaField::new('kittenData')
                ->setFormTypeOption('mapped', false)
                ->setFormTypeOption('attr', [
                    'id' => 'kitten-json-data',
                    'style' => 'display:none;',
                ])
                ->setFormTypeOption('data', json_encode([
                    'litters' => $this->litterRepository->findAllWithBreed(),
                    'colors' => $this->colorRepository->findAllWithBreed(),
                ]))
                ->onlyOnForms(),
        ];
    }


}
