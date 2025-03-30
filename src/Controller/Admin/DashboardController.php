<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
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

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Grande Linci');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::subMenu('Entities', 'fa fa-home')->setSubItems([
            MenuItem::linkToCrud('Bookings', 'fas fa-list', Booking::class),
            MenuItem::linkToCrud('Cats', 'fas fa-list', Cat::class),
            MenuItem::linkToCrud('Colors', 'fas fa-list', Color::class),
            MenuItem::linkToCrud('Guest requests', 'fas fa-list', GuestRequest::class),
            MenuItem::linkToCrud('Kittens', 'fas fa-list', Kitten::class),
            MenuItem::linkToCrud('Liters', 'fas fa-list', Litter::class),
            MenuItem::linkToCrud('Kittens statuses', 'fas fa-list', KittenStatus::class),
        ]);
        yield MenuItem::linkToRoute('Homepage', 'fa fa-home', 'admin_select_litter');
    }
}
