<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\LitterService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Booking;
use App\Entity\Kitten;
use App\Form\BookingType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AvailableKittensController extends AbstractController
{
    #[Route('/available-kittens/{id}', name: 'app_available_kittens', methods: ['GET', 'POST'])]
    public function index($id, LitterService $litterService, Request $request, EntityManagerInterface $entityManager): Response
    {
        $litters = $litterService->getAllLitters();
        $buttons = [];
        foreach ($litters as $litter) {
            $status = $litterService->getLitterStatus($litter);
            $buttons[] = [
                'name' => $litter->getName(),
                'id' => $litter->getId(),
                'status' => $status,
            ];
        }

        if ($id && $id !== "default") {
            [$litter, $mom, $dad] = $litterService->getLitterById($id);
            $kittens = $litterService->getAllKittens($litter);
        } else {
            [$litter, $mom, $dad] = $litterService->getLitter();
            $kittens = $litterService->getAllKittens($litter);
        }

        // Создаем форму бронирования
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $kittenId = $request->request->get('kitten_id');
            $kitten = $entityManager->getRepository(Kitten::class)->find($kittenId);

            if (!$kitten) {
                throw $this->createNotFoundException('Kitten not found');
            }

            $booking->setKitten($kitten);
            $booking->setUser($this->getUser());
            $booking->setCreatedAt(new \DateTime());
            $booking->setStatus('В ожидании');

            $entityManager->persist($booking);
            $entityManager->flush();

            $this->addFlash('success', 'Ваша заявка отправлена!');
            return $this->redirectToRoute('app_available_kittens', ['id' => $id]);
        }

        return $this->render('available_kittens/index.html.twig', [
            'controller_name' => 'AvailableKittensController',
            'buttons' => $buttons,
            'litter' => $litter,
            'mother' => $mom,
            'father' => $dad,
            'kittens' => $kittens,
            'form' => $form->createView(),
        ]);
    }
}
