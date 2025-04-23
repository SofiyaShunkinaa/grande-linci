<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AboutUsController extends AbstractController
{
    #[Route('/about-us', name: 'app_about_us')]
    public function index(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser();
        $book = false;

        if ($user) {
            var_dump($bookingRepository->userHasNewUpdates($user));
            $book = $bookingRepository->userHasNewUpdates($user);
        }
        return $this->render('about_us/index.html.twig', [
            'controller_name' => 'AboutUsController',
            'book' => $book
        ]);
    }
}
