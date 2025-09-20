<?php

namespace App\Services;

use App\Entity\Carpooling;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\UserReview;
use Doctrine\ORM\EntityManagerInterface;

final class ReviewManagerService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function publishReview(Review $review, User $user, User $driverUser, Carpooling $carpooling, int $grade): static
    {

        $review->setRoleId(implode(',', $user->getRoles()))
            ->setStatut('TO_BE_CHECKED');
        $this->em->persist($review);

        $userReview = new UserReview();
        $userReview->setUser($user)
            ->setAffectedUser($driverUser)
            ->setReview($review)
            ->setCarpooling($carpooling);
        $this->em->persist($userReview);

        $driverUser->updateGrade($grade);
        $this->em->persist($driverUser);

        $this->em->flush();

        return $this;
    }
}
