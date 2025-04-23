<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AboutBreedController extends AbstractController
{
    #[Route('/about-breed/main-coon', name: 'app_about_main_coon')]
    public function index(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser();
        $book = false;

        if ($user) {
            $book = $bookingRepository->userHasNewUpdates($user);
        }
        return $this->render('about_breed/main_coon.html.twig', [
            'controller_name' => 'AboutBreedController',
            'book' => $book
        ]);
    }
}
