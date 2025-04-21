<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use App\Repository\BreedRepository;
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
    #[Route('/available-kittens/{keyWord}/{id}', name: 'app_available_kittens', requirements: ['keyWord' => '[^/]+'], defaults: ['id' => 'default', 'keyWord' => 'main-coons'], methods: ['GET', 'POST'])]
    public function index($id, LitterService $litterService, Request $request, EntityManagerInterface $entityManager, BookingRepository $bookingRepository, ?string $keyWord, BreedRepository $breedRepository): Response
    {
        $breed = $breedRepository->findOneBy(['keyWord' => $keyWord]);
        $litters = $litterService->getAllLitters($breed);
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
            [$litter, $mom, $dad] = $litterService->getLitterbyBreed($breed);
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
            return $this->redirectToRoute('app_available_kittens', [
                'id' => $id,
                'keyWord' => $keyWord,
            ]);
        }

        return $this->render('available_kittens/index.html.twig', [
            'controller_name' => 'AvailableKittensController',
            'buttons' => $buttons,
            'litter' => $litter,
            'mother' => $mom,
            'father' => $dad,
            'kittens' => $kittens,
            'form' => $form->createView(),
            'bookings' => $bookingRepository->findAll(),
        ]);
    }

    #[Route('/litter/change', name: 'change_litter', methods: ['POST'])]
    public function changeLitter(Request $request, LitterService $litterService): JsonResponse
    {
        $litterId = $request->request->get('litterId');

        if (!$litterId) {
            return new JsonResponse(['error' => 'Litter ID is missing'], 400);
        }

        [$litter, $mother, $father] = $litterService->getLitterById($litterId);
        if (!$litter) {
            return new JsonResponse(['error' => 'Litter not found'], 404);
        }

        $kittens = $litterService->getAllKittens($litter);

        return new JsonResponse([
            'name' => $litter->getName(),
            'date' => $litter->getDate()->format('d.m.Y'),
            'mother' => [
                'name' => $mother->getName(),
                'image' => $mother->getImageLink(),
            ],
            'father' => [
                'name' => $father->getName(),
                'image' => $father->getImageLink(),
            ],
            'kittens' => array_map(fn($kitten) => [
                'id' => $kitten->getId(),
                'name' => $kitten->getName(),
                'image' => $kitten->getImageLink(),
                'status' => $kitten->getKittenStatus(),
            ], $kittens),
        ]);
    }

}
