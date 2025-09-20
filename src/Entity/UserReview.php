<?php

namespace App\Entity;

use App\Repository\UserReviewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserReviewRepository::class)]
class UserReview
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userReviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Review $review = null;

    #[ORM\ManyToOne(inversedBy: 'userReviews')]
    private ?User $affected_user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Carpooling $carpooling = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): static
    {
        $this->review = $review;

        return $this;
    }

    public function getAffectedUser(): ?user
    {
        return $this->affected_user;
    }

    public function setAffectedUser(?user $affected_user): static
    {
        $this->affected_user = $affected_user;

        return $this;
    }

    public function getCarpooling(): ?Carpooling
    {
        return $this->carpooling;
    }

    public function setCarpooling(?Carpooling $carpooling): static
    {
        $this->carpooling = $carpooling;

        return $this;
    }
}
