<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\LitterService;
use App\Entity\GuestRequest;
use App\Form\RequestType;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_home')]
    public function index(LitterService $litterService, Request $request, BookingRepository $bookingRepository): Response
    {
        $litterData = $litterService->getLitter();

        // Создаем объект для формы
        $guestRequest = new GuestRequest();
        $form = $this->createForm(RequestType::class, $guestRequest);
        $form->handleRequest($request);

        // Если данных нет, рендерим с пустыми значениями
        if ($litterData === null) {
            return $this->render('home/index.html.twig', [
                'litter' => null,
                'kittens' => [],
                'mother' => null,
                'father' => null,
                'form' => $form->createView(),
            ]);
        }

        // Получаем данные о котенках
        [$litter, $mom, $dad] = $litterService->getLitter();
        $kittens = $litterService->get5Kittens($litter);

        // Обрабатываем отправку формы
        if ($form->isSubmitted() && $form->isValid()) {
            $guestRequest->setRequestDate(new \DateTime());  // Устанавливаем текущую дату запроса
            $this->entityManager->persist($guestRequest);
            $this->entityManager->flush();

            $this->addFlash('success', 'Ваша форма успешно отправлена!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'litter' => $litter,
            'kittens' => $kittens,
            'mother' => $mom,
            'father' => $dad,
            'form' => $form->createView(),
            'bookings' => $bookingRepository->findAll(),
        ]);
    }
}
