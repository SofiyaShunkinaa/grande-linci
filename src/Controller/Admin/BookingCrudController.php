<?php
namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\KittenStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;


class BookingCrudController extends AbstractCrudController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW) // Запрещает добавление новых записей
            ->add(Crud::PAGE_EDIT, Action::INDEX) // Добавляем кнопку "К списку" на страницу редактирования
            ->update(Crud::PAGE_EDIT, Action::INDEX, function (Action $action) {
                return $action->setLabel('Назад к записям')
                    ->setIcon('fa fa-arrow-left');
        });
    }

    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_EDIT) {
            $request = $this->getContext()->getRequest();
            $id = $request->get('entityId');

            $booking = $this->entityManager->getRepository(Booking::class)->find($id);
            if ($booking && !$booking->isViewedByAdmin()) {
                $booking->setIsViewedByAdmin(true);
                $this->entityManager->flush();
            }
        }

        return [
            DateTimeField::new('createdAt', 'Создано')
                ->setFormat('dd.MM.yyyy HH:mm')
                ->setFormTypeOption('disabled', true),

            // Выбор пользователя
            AssociationField::new('user', 'Пользователь')
                ->setCrudController(UserCrudController::class)
                ->setRequired(true)
                ->setFormTypeOption('disabled', true),

            // Поля, которые будут обновляться динамически
            TextField::new('userDetails', 'Информация о пользователе')
                ->setFormTypeOption('disabled', true)
                ->hideOnIndex(),

            // Выбор котенка
            AssociationField::new('kitten', 'Котенок')
                ->setCrudController(KittenCrudController::class)
                ->setRequired(true)
                ->setFormTypeOption('disabled', true),

            TextField::new('kittenDetails', 'Информация о котенке')
                ->setFormTypeOption('disabled', true)
                ->setCustomOption('renderAsString', true)
                ->formatValue(fn ($value, $entity) => $entity->getKitten()?->getDetails() ?? 'Нет данных')
                ->hideOnIndex(),

            // Статус
            ChoiceField::new('status', 'Статус')
                ->setChoices([
                    'В ожидании' => 'В ожидании',
                    'Подтверждено' => 'Подтверждено',
                    'Отклонено' => 'Отклонено'
                ]),
        ];
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof Booking){
            $status = $entityInstance->getStatus();

            if ($status === 'Подтверждено') {
                $kitten = $entityInstance->getKitten();

                if ($kitten) {
                    $reservedStatus = $entityManager->getRepository(KittenStatus::class)->findOneBy(['name' => 'Reserved']);

                    if ($reservedStatus) {
                        $kitten->setKittenStatus($reservedStatus);
                        $entityManager->persist($kitten);
                        $entityManager->flush(); // Применяем изменения к котенку
                    }
                }
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }


    // Переопределяем метод сохранения сущности
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Booking) {
            $status = $entityInstance->getStatus();

            if ($status === 'Подтверждено') {
                $kitten = $entityInstance->getKitten();

                if ($kitten) {
                    $reservedStatus = $entityManager->getRepository(KittenStatus::class)->findOneBy(['name' => 'Reserved']);

                    if ($reservedStatus) {
                        $kitten->setKittenStatus($reservedStatus);
                        $entityManager->persist($kitten);
                        $entityManager->flush(); // Добавляем flush, чтобы изменения записались в БД
                    }
                }
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

}
