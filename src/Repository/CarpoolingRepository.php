<?php

namespace App\Repository;

use App\Entity\Car;
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
            ->setParameter('statut', "ONLINE")
            ->setMaxResults(50)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findAllByUserAndStatut(User $user, string $statut): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.created_by = :user')
            ->andWhere('c.statut = :statut')
            ->setParameter('user', $user)
            ->setParameter('statut', $statut)
            ->setMaxResults(50)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findAllByUserAndDate(User $user, int $past = 0): array
    {
        $today = new \DateTimeImmutable('today'); // 00:00:00
        $tomorrow = $today->modify('+1 day');

        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.created_by = :user_id')
            ->andWhere('c.statut = :statut')
            ->setParameter('user_id', $user)
            ->setParameter('statut', "Online")
            ->setMaxResults(50);

        switch ($past) {
            case -1:
                $qb->andWhere('c.start_date < :today')
                    ->setParameter('today', $today);
                break;
            case 0:
                $qb->andWhere('c.start_date >= :today')
                    ->andWhere('c.start_date < :tomorrow')
                    ->setParameter('tomorrow', $tomorrow)
                    ->setParameter('today', $today);
                break;
            case 1:
                $qb->andWhere('c.start_date >= :tomorrow')
                    ->setParameter('tomorrow', $tomorrow);
                break;
            default:
                $qb->andWhere('c.start_date >= :today')
                    ->andWhere('c.start_date < :tomorrow')
                    ->setParameter('tomorrow', $tomorrow)
                    ->setParameter('today', $today);
                break;
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findBySearchCarpool(
        string $startPlace,
        string $endPlace,
        DateTimeImmutable $date,
        ?User $user,
    ): array {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.start_place = :startPlace')
            ->andWhere('c.end_place = :endPlace')
            ->andWhere('c.start_date > :date')
            ->andWhere('c.available_seat > 0')
            ->andWhere('c.statut = :statut')
            ->setParameter('startPlace', $startPlace)
            ->setParameter('endPlace', $endPlace)
            ->setParameter('date', $date)
            ->setParameter('statut', 'ONLINE');
        if ($user !== null) {
            $qb->andWhere('c.created_by != :user')
                ->setParameter('user', $user);
        }
        return $qb->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findCarpoolByUserAndCar(Car $car, User $user): bool
    {
        $qb =  $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.created_by = :user')
            ->andWhere('c.car = :car')
            ->setParameter('user', $user)
            ->setParameter('car', $car)
            ->getQuery()
            ->getSingleScalarResult();
        // dd((int)$qb);
        return (int)$qb > 0;
    }
}
