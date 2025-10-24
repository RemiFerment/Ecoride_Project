<?php

namespace App\Repository;

use App\Entity\UserReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserReview>
 */
class UserReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserReview::class);
    }


    public function findByToBeChecked(): array
    {
        return $this->createQueryBuilder('u')
            ->join('u.review', 'r')
            ->addSelect('r')
            ->andWhere('r.statut = :status')
            ->setParameter('status', 'TO_BE_CHECKED')
            ->setMaxResults(500)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return UserReview[] Returns an array of UserReview objects with gradeGiven < 3
     */
    public function findByLowGrade(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.gradeGiven < :grade')
            ->setParameter('grade', 3)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return UserReview[] Returns an array of UserReview objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserReview
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
