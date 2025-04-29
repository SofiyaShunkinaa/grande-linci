<?php

namespace App\Repository;

use App\Entity\Litter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Breed;

/**
 * @extends ServiceEntityRepository<Litter>
 */
class LitterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Litter::class);
    }

    public function findOneByIsActive(): ?Litter
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByIsActiveBreed(Breed $breed): ?Litter
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.isActive = :isActive')
            ->andWhere('l.breed = :breed')
            ->setParameter('isActive', true)
            ->setParameter('breed', $breed)
            ->orderBy('l.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByBreed(Breed $breed): ?Litter
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.breed = :breed')
            ->setParameter('breed', $breed)
            ->orderBy('l.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return Litter[] Returns an array of Litter objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

}
