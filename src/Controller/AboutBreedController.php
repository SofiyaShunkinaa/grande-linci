<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BreedRepository;


class AboutBreedController extends AbstractController
{
    #[Route('/about-breed/{keyWord}', name: 'app_about_main_coon', defaults: ['keyWord' => ''])]
    public function index(?string $keyWord, BreedRepository $breedRepository): Response
    {
        if(!$keyWord) $keyWord = 'main-coons';
        $breed = $breedRepository->findOneBy(['keyWord' => $keyWord]);
        if($breed->getKeyWord() == 'main-coons'){
            return $this->render('about_breed/main_coon.html.twig', [
                'controller_name' => 'AboutBreedController',
            ]);
        }else{
            return $this->render('about_breed/main_coon.html.twig', [
                'controller_name' => 'AboutBreedController',
            ]);
        }
    }

}
