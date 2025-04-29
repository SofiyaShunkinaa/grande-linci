<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function userHasNewUpdates(User $user): bool
    {
        return $this->createQueryBuilder('b')
                ->select('COUNT(b.id)')
                ->where('b.user = :user')
                ->andWhere('b.isViewed = false')
                ->andWhere('b.status != :pendingStatus')
                ->setParameter('user', $user)
                ->setParameter('pendingStatus', 'В ожидании')
                ->getQuery()
                ->getSingleScalarResult() > 0; // 👈 сравнение!
    }

    public function hasUnseen(): bool
    {
        return (bool) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.isViewedByAdmin = false')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Booking[] Returns an array of Booking objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Booking
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
