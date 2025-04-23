<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GalleryController extends AbstractController
{
    #[Route('/gallery', name: 'app_gallery')]
    public function index(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser();
        $book = false;

        if ($user) {
            var_dump($bookingRepository->userHasNewUpdates($user));
            $book = $bookingRepository->userHasNewUpdates($user);
        }
        return $this->render('gallery/index.html.twig', [
            'controller_name' => 'GalleryController',
            'book' => $book,
        ]);
    }
}
