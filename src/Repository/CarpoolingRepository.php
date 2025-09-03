<?php

namespace App\Repository;

use App\Entity\Carpooling;
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
    public function findAllByUserId(int $user_id): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.create_by = :user_id')
            ->andWhere('c.statut = :statut')
            ->setParameter('user_id', $user_id)
            ->setParameter('statut', "Online")
            ->setMaxResults(50)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findAllByUserAndDate(int $user_id, bool $past = false): array
    {
        $date = new DateTimeImmutable();

        return $this->createQueryBuilder('c')
            ->andWhere('c.create_by = :user_id')
            ->andWhere('c.statut = :statut')
            ->andWhere($past ? 'c.start_date < :now' : 'c.start_date >= :now')
            ->setParameter('user_id', $user_id)
            ->setParameter('statut', "Online")
            ->setParameter('now', $date)
            ->setMaxResults(50)
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
