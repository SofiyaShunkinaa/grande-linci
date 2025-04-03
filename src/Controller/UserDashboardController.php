<?php

namespace App\Controller;

use App\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Entity\Chat;
use Doctrine\ORM\EntityManagerInterface;


#[IsGranted('ROLE_USER')]
class UserDashboardController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/user/dashboard', name: 'app_user_dashboard')]
    public function index(AuthorizationCheckerInterface $authChecker): Response
    {
        $user = $this->getUser();

        // Находим чат пользователя
        $chat = $this->entityManager->getRepository(Chat::class)->findOneBy(['user' => $user]);

        // Получаем заявки пользователя
        $bookings = $this->entityManager->getRepository(Booking::class)->findBy(['user' => $user]);

        // Проверяем, авторизован ли пользователь
        if (!$authChecker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user_dashboard/index.html.twig', [
            'chat' => $chat,
            'bookings' => $bookings, // Передаем заявки в шаблон
        ]);
    }

    #[Route('/dashboard/bookings/cancel/{id}', name: 'cancel_booking', methods: ['POST'])]
    public function cancelBooking(Booking $booking, EntityManagerInterface $entityManager, Request $request)
    {
        // Проверяем, принадлежит ли заявка текущему пользователю
        if ($booking->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Удаляем заявку
        $entityManager->remove($booking);
        $entityManager->flush();

        // Добавляем уведомление об успешном удалении
        $this->addFlash('success', 'Бронирование успешно отменено.');

        // Перенаправляем обратно в личный кабинет
        return $this->redirectToRoute('app_user_dashboard');
    }

}
