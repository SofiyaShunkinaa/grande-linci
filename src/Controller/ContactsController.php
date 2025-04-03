<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\GuestRequest;
use App\Form\RequestType;

class ContactsController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    // Инициализируем entityManager через конструктор
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/contacts', name: 'app_contacts')]
    public function index(Request $request): Response
    {
        // Создаем объект для формы
        $guestRequest = new GuestRequest();
        $form = $this->createForm(RequestType::class, $guestRequest);
        $form->handleRequest($request);

        // Обрабатываем отправку формы
        if ($form->isSubmitted() && $form->isValid()) {
            $guestRequest->setRequestDate(new \DateTime());  // Устанавливаем текущую дату запроса
            $this->entityManager->persist($guestRequest);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your request has been submitted successfully.');
        }

        return $this->render('contacts/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
