<?php

namespace App\Controller\Admin;

use App\Entity\Litter;
use App\Repository\CatRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\Validator\Constraints as Assert;

class LitterCrudController extends AbstractCrudController
{
    private CatRepository $catRepository;

    public function __construct(CatRepository $catRepository)
    {
        $this->catRepository = $catRepository;
    }

    public static function getEntityFqcn(): string
    {
        return Litter::class;
    }

    public function createEntity(string $entityFqcn)
    {
        return new Litter(); // новая сущность, breed ещё не выбрана
    }

    public function configureFields(string $pageName): iterable
    {
        /** @var Litter|null $litter */
        $litter = $this->getContext()?->getEntity()?->getInstance();

        $selectedBreed = $litter?->getBreed();

        return [
            AssociationField::new('breed'),

            TextField::new('name')
                ->setFormTypeOption('constraints', [
                    new Assert\Length([
                        'max' => 1, // ограничение на один символ
                        'maxMessage' => 'Поле должно содержать не более одного символа',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z]$/', // регулярное выражение для латиницы
                        'message' => 'Поле должно содержать только одну латинскую букву',
                    ]),
                ]),
            DateField::new('date')
                ->setFormTypeOption('attr', [
                    'max' => (new \DateTime())->format('Y-m-d'), // ограничиваем выбор до текущей даты
                ]),

            AssociationField::new('catMother')
                ->setFormTypeOptions([
                    'query_builder' => function (CatRepository $repo) use ($selectedBreed) {
                        $qb = $repo->createQueryBuilder('c')
                            ->where('c.gender = :female')
                            ->setParameter('female', 2)
                            ->orderBy('c.name', 'ASC');

                        if ($selectedBreed) {
                            $qb->andWhere('c.breed = :breed')
                                ->setParameter('breed', $selectedBreed);
                        }

                        return $qb;
                    },
                ]),

            AssociationField::new('catFather')
                ->setFormTypeOptions([
                    'query_builder' => function (CatRepository $repo) use ($selectedBreed) {
                        $qb = $repo->createQueryBuilder('c')
                            ->where('c.gender = :male')
                            ->setParameter('male', 1)
                            ->orderBy('c.name', 'ASC');

                        if ($selectedBreed) {
                            $qb->andWhere('c.breed = :breed')
                                ->setParameter('breed', $selectedBreed);
                        }

                        return $qb;
                    },
                ]),

            BooleanField::new('isActive'),
            TextareaField::new('catsData')
                ->setFormTypeOption('mapped', false)
                ->setFormTypeOption('attr', [
                    'id' => 'cats-json-data',
                    'style' => 'display:none;',
                ])
                ->setFormTypeOption('data', json_encode($this->catRepository->findAllWithBreedAndGender()))
                ->onlyOnForms(),

        ];
    }
}
