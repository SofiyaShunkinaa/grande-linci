<?php
namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\KittenStatus;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\EntityManagerInterface;

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

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('createdAt', 'Создано')
                ->setFormat('dd.MM.yyyy HH:mm')
                ->setFormTypeOption('disabled', true),

            // Выбор пользователя
            AssociationField::new('user', 'Пользователь')
                ->setCrudController(UserCrudController::class)
                ->setRequired(true),

            // Поля, которые будут обновляться динамически
            TextField::new('userDetails', 'Информация о пользователе')
                ->setFormTypeOption('disabled', true)
                ->hideOnIndex(),

            // Выбор котенка
            AssociationField::new('kitten', 'Котенок')
                ->setCrudController(KittenCrudController::class)
                ->setRequired(true),

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

    // Переопределяем метод сохранения сущности
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Booking) {
            // Получаем текущий статус бронирования
            $status = $entityInstance->getStatus();

            // Проверяем, если статус "Подтверждено", то меняем статус котенка
            if ($status === 'Подтверждено') {
                $kitten = $entityInstance->getKitten();

                if ($kitten) {
                    // Получаем статус "Reserved" из базы данных
                    $reservedStatus = $entityManager->getRepository(KittenStatus::class)->findOneBy(['name' => 'Reserved']);

                    if ($reservedStatus) {
                        // Обновляем статус котенка
                        $kitten->setKittenStatus($reservedStatus);
                        $entityManager->persist($kitten); // Сохраняем изменения для котенка
                    }
                }
            }
        }

        parent::persistEntity($entityManager, $entityInstance);  // Важно вызвать родительский метод для нормального сохранения
    }
}
