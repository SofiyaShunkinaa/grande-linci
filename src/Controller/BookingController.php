<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Kitten;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/kitten/{id}/book', name: 'kitten_book', methods: ['POST'])]
    public function book(Kitten $kitten, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $booking = new Booking();
        $booking->setUser($this->getUser());
        $booking->setKitten($kitten);
        $booking->setCreatedAt(new \DateTime());
        $booking->setStatus('В ожидании');

        $entityManager->persist($booking);
        $entityManager->flush();

        $this->addFlash('success', 'Заявка отправлена!');
        return $this->redirect($request->headers->get('referer'));


    }
}
