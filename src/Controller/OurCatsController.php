<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CatService;
use App\Repository\BreedRepository;

class OurCatsController extends AbstractController
{
    #[Route('/our-cats/{keyWord}', name: 'app_our_cats', defaults: ['keyWord' => ''])]
    public function index(?string $keyWord, CatService $catService, BreedRepository $breedRepository): Response
    {
        $breed = $breedRepository->findOneBy(['keyWord' => $keyWord]);
        $females = $catService->getFemaleCats($breed);
        $males = $catService->getMaleCats($breed);
        $cats = [];
        $femaleIndex = 0;
        $maleIndex = 0;
       
        while ($femaleIndex < count($females) || $maleIndex < count($males)) {
      
            $female = $femaleIndex < count($females) ? $females[$femaleIndex] : null;
            $male = $maleIndex < count($males) ? $males[$maleIndex] : null;
        
            if ($female !== null && $male !== null) {
                $cats[] = [
                    'female' => $female,
                    'male' => null,
                ];
                $cats[] = [
                    'female' => null,
                    'male' => $male,
                ];
                $femaleIndex++;
                $maleIndex++;
            } elseif ($female !== null) {
                $cats[] = [
                    'female' => $female,
                    'male' => null,
                ];
                $femaleIndex++;
        
                if ($femaleIndex < count($females)) {
                    $cats[] = [
                        'female' => $females[$femaleIndex],
                        'male' => null,
                    ];
                    $femaleIndex++;
                }
            } elseif ($male !== null) {
          
                $cats[] = [
                    'female' => null,
                    'male' => $male,
                ];
                $maleIndex++;
            }
        }
        
        $catsCount = count($cats);
        
     
        return $this->render('our_cats/index.html.twig', [
            'controller_name' => 'OurCatsController',
            'cats' => $cats,
            'count' => $catsCount,
            'title' => $breed->getName(),
        ]);
    }
}
