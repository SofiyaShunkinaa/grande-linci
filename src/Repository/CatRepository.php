<?php

namespace App\Repository;

use App\Entity\Cat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cat>
 */
class CatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cat::class);
    }

   /**
    * @return Cat[] 
    */
   public function findByGender(string $gender): array
   {
       return $this->createQueryBuilder('c')
           ->andWhere('c.gender = :gender')
           ->setParameter('gender', $gender)
           ->orderBy('c.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

    public function findAllWithBreedAndGender(): array
    {
        $cats = $this->createQueryBuilder('c')
            ->leftJoin('c.breed', 'b')->addSelect('b')
            ->leftJoin('c.gender', 'g')->addSelect('g')
            ->getQuery()
            ->getResult();

        return array_map(function (Cat $cat) {
            return [
                'id' => $cat->getId(),
                'name' => $cat->getName(),
                'breed' => $cat->getBreed()?->getId(),
                'gender' => $cat->getGender()?->getId(), // Предположим: 1 = самец, 2 = самка
            ];
        }, $cats);
    }

//    public function findOneBySomeField($value): ?Cat
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
