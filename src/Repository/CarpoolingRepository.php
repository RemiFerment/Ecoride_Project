<?php

namespace App\Repository;

use App\Entity\Carpooling;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Carpooling>
 */
class CarpoolingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Carpooling::class);
    }

    /**
     * @return Carpooling[] Returns an array of Carpooling objects
     */
    public function findAllByUserId(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.created_by = :user')
            ->andWhere('c.statut = :statut')
            ->setParameter('user', $user)
            ->setParameter('statut', "Online")
            ->setMaxResults(50)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findAllByUserAndDate(User $user, bool $past = false): array
    {
        $date = new DateTimeImmutable();

        return $this->createQueryBuilder('c')
            ->andWhere('c.created_by = :user_id')
            ->andWhere('c.statut = :statut')
            ->andWhere($past ? 'c.start_date < :now' : 'c.start_date >= :now')
            ->setParameter('user_id', $user)
            ->setParameter('statut', "Online")
            ->setParameter('now', $date)
            ->setMaxResults(50)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBySearchCarpool(
        string $startPlace,
        string $endPlace,
        DateTimeImmutable $date,
        User $user,
    ): array {
        return $this->createQueryBuilder('c')
            ->andWhere('c.start_place = :startPlace')
            ->andWhere('c.end_place = :endPlace')
            ->andWhere('c.start_date > :date')
            ->andWhere('c.created_by != :user')
            ->andWhere('c.available_seat > 0')
            ->setParameter('startPlace', $startPlace)
            ->setParameter('endPlace', $endPlace)
            ->setParameter('date', $date)
            ->setParameter('user', $user)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    //    public function findOneBySomeField($value): ?Carpooling
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
