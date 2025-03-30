<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\GuestRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RequestType;


class ContactsController extends AbstractController
{
    #[Route('/contacts', name: 'app_contacts')]
    public function index(Request $request): Response
    {
        $guestRequest = new GuestRequest();
        $form = $this->createForm(RequestType::class, $guestRequest);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($guestRequest);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your request has been submitted successfully.');
        }

        return $this->render('contacts/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
