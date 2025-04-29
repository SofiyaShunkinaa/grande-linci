<?php

namespace App\Service;

use App\Entity\Breed;
use App\Entity\Litter;
use App\Repository\LitterRepository;
use App\Repository\KittenRepository;
use App\Repository\CatRepository;
use App\Entity\Cat;

class LitterService
{
    private LitterRepository $litterRepository;
    private KittenRepository $kittenRepository;
    private CatRepository $catRepository;

    public function __construct(LitterRepository $litterRepository, KittenRepository $kittenRepository, CatRepository $catRepository)
    {
        $this->litterRepository = $litterRepository;
        $this->kittenRepository = $kittenRepository;
        $this->catRepository = $catRepository;
    }

    public function getActiveLitter(): ?Litter
    {
        $litter = $this->litterRepository->findOneByIsActive();
        return $litter;
    }

    public function getLitter(): ?array
    {
        $litter = $this->getActiveLitter();

        if (!$litter) {
            return null;
        }

        $mom = $this->catRepository->findOneBy(['id' => $litter->getCatMother()]);
        $dad = $this->catRepository->findOneBy(['id' => $litter->getCatFather()]);

        return [$litter, $mom, $dad];
    }

    public function getLitterByBreed(Breed $breed): ?array
    {
        $litter = $this->litterRepository->findOneByIsActiveBreed($breed) ? $this->litterRepository->findOneByBreed($breed) : [];

        if (!$litter) {
            $litter = $this->litterRepository->findOneByBreed($breed);
        }

        if (!$litter) {
            return null;
        }

        $mom = $this->catRepository->findOneBy(['id' => $litter->getCatMother()]);
        $dad = $this->catRepository->findOneBy(['id' => $litter->getCatFather()]);

        return [$litter, $mom, $dad];
    }

    public function getLitterById(int $id): ?array
    {
        $litter = $this->litterRepository->findOneBy(['id' => $id]);

        if (!$litter) {
            return null;
        }

        $mom = $this->catRepository->findOneBy(['id' => $litter->getCatMother()]);
        $dad = $this->catRepository->findOneBy(['id' => $litter->getCatFather()]);

        return [$litter, $mom, $dad];
    }

    public function get5Kittens(Litter $litter): array
    {
        return $this->kittenRepository->find5ByLitter($litter->getId());
    }

    public function getAllKittens(Litter $litter): array
    {
        return $this->kittenRepository->findAllByLitter($litter->getId());
    }

    public function getLitterStatus(Litter $litter): bool
    {
        $kittens = $this->getAllKittens($litter);
        $totalCount = count($kittens);
        $atHomeCount = 0;
        foreach($kittens as $kitten){
            if($kitten->getKittenStatus() == 'At home'){
                $atHomeCount++;
            }
        }
        return $totalCount !== $atHomeCount;
    }

    public function getAllLitters(Breed $breed): ?array
    {
        $litters = $this->litterRepository->findBy(['breed' => $breed]);
        return $litters;
    }
}