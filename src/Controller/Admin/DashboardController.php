<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\GuestRequestRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Cat;
use App\Entity\Color;
use App\Entity\GuestRequest;
use App\Entity\Kitten;
use App\Entity\KittenStatus;
use App\Entity\Litter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function __construct(
        private BookingRepository $bookingRepository,
        private GuestRequestRepository $guestRequestRepository
    ) {}

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Grande Linci');
    }

    public function configureMenuItems(): iterable
    {
        // Проверяем наличие новых уведомлений для каждой сущности
        $hasNewBookings = $this->bookingRepository->hasUnseen();
        $hasNewRequests = $this->guestRequestRepository->hasUnseen();

        // Проверяем наличие уведомлений для всех сущностей
        $hasNewEntities = $hasNewBookings || $hasNewRequests;

        // Создаем родительское меню с уведомлением, если есть новые записи
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::subMenu('Entities' . ($hasNewEntities ? ' 🔴' : ''), 'fa fa-home')->setSubItems([
            MenuItem::linkToCrud(
                'Bookings' . ($hasNewBookings ? ' 🔴' : ''),
                'fas fa-list',
                Booking::class
            ),
            MenuItem::linkToCrud(
                'Guest requests' . ($hasNewRequests ? ' 🔴' : ''),
                'fas fa-list',
                GuestRequest::class
            ),
            MenuItem::linkToCrud('Cats', 'fas fa-list', Cat::class),
            MenuItem::linkToCrud('Colors', 'fas fa-list', Color::class),
            MenuItem::linkToCrud('Kittens', 'fas fa-list', Kitten::class),
            MenuItem::linkToCrud('Liters', 'fas fa-list', Litter::class),
            MenuItem::linkToCrud('Kittens statuses', 'fas fa-list', KittenStatus::class),
        ]);

        yield MenuItem::linkToRoute('Homepage', 'fa fa-home', 'admin_select_litter');
    }

}
