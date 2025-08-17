<?php

namespace App\Entity;

use App\Repository\UserCarpollingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCarpollingRepository::class)]
class UserCarpolling
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column]
    private ?int $carpooling_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getCarpoolingId(): ?int
    {
        return $this->carpooling_id;
    }

    public function setCarpoolingId(int $carpooling_id): static
    {
        $this->carpooling_id = $carpooling_id;

        return $this;
    }
}
