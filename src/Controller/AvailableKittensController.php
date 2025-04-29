<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\LitterService;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\BreedRepository;

class AvailableKittensController extends AbstractController
{
    #[Route('/available-kittens/{keyWord}/{id}', name: 'app_available_kittens', requirements: ['keyWord' => '[^/]+'], defaults: ['id' => 'default'], methods: ['GET'])]
    public function index(?string $keyWord,$id, LitterService $litterService, Request $request, BreedRepository $breedRepository): Response
    {
        if(!$keyWord) $keyWord = 'main-coons';
        $breed = $breedRepository->findOneBy(['keyWord' => $keyWord]);
        $litters = $litterService->getAllLitters($breed);
        $buttons = [];
        foreach($litters as $litter){
            $status = $litterService->getLitterStatus($litter);
            
            $buttons[] = [
                'name' => $litter->getName(),
                'id' => $litter->getId(),
                'status' => $status,
            ];
        }
        if($id && $id !== "default"){
            [$litter, $mom, $dad] = $litterService->getLitterById($id); 
            $kittens = $litterService->getAllKittens($litter);
        }
        else{
            [$litter, $mom, $dad] = $litterService->getLitterbyBreed($breed);
            $kittens = $litterService->getAllKittens($litter);
        }
       
        if($request->isXmlHttpRequest()){
            return new JsonResponse([
                'buttons' => $buttons,
                'litter' => [
                    'name' => $litter->getName(),
                    'date' => $litter->getDate()->format('Y-m-d'),
                ],
                'mother' => [
                    'name' => $mom->getName(),
                    'imageLink' => $mom->getImageLink(),
                ],
                'father' => [
                    'name' => $dad->getName(),
                    'imageLink' => $dad->getImageLink(),
                ],
                'kittens' => array_map(function($kitten) {
                    return [
                        'name' => $kitten->getName(),
                        'imageLink' => $kitten->getImageLink(),
                        'kittenStatus' => $kitten->getKittenStatus()->getName(),
                    ];
                }, $kittens),
                'title' => $breed->getName(),
                'keyWord' => $keyWord,
            ]);
        }
        else{
            return $this->render('available_kittens/index.html.twig', [
                'controller_name' => 'AvailableKittensController',
                'buttons' => $buttons,
                'litter' => $litter,
                'mother' => $mom,
                'father' => $dad,
                'kittens' => $kittens,
                'title' => $breed->getName(),
                'keyWord' => $keyWord,
            ]);
        }
    }
}
