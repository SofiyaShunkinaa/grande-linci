<?php

namespace App\Service;

use App\Repository\CatRepository;
use App\Entity\Cat;
use App\Entity\Breed;

class CatService
{
    private CatRepository $catRepository;

    public function __construct(CatRepository $catRepository)
    {
        $this->catRepository = $catRepository;
    }

    public function getMaleCats(Breed $breed): ?array
    {
        $maleCats = $this->catRepository->findBy([
            'gender' => '1',
            'breed' => $breed,
        ]);

        if(!$maleCats){
            return [];
        }

        return $maleCats;
    }

    public function getFemaleCats(Breed $breed): ?array
    {
        $femaleCats = $this->catRepository->findBy([
            'gender' => '2',
            'breed' => $breed,
        ]);

        if(!$femaleCats){
            return [];
        }

        return $femaleCats;
    }

}