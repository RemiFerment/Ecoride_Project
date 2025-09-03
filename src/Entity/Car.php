<?php

namespace App\Entity;

use App\Repository\CarRepository;
use App\Validator\CityCheck;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $model = null;

    #[ORM\Column(length: 50)]
    private ?string $registration = null;

    #[ORM\Column(length: 50)]
    private ?string $power_engine = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $first_date_registration = null;

    // #[ORM\Column]
    // private ?int $marque_id = null;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\ManyToOne(targetEntity: Marque::class)]
    private ?Marque $marque = null;

    #[ORM\Column]
    private ?int $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getRegistration(): ?string
    {
        return $this->registration;
    }

    public function setRegistration(string $registration): static
    {
        $this->registration = $registration;

        return $this;
    }

    public function getPowerEngine(): ?string
    {
        return $this->power_engine;
    }

    public function setPowerEngine(string $power_engine): static
    {
        $this->power_engine = $power_engine;

        return $this;
    }

    public function getFirstDateRegistration(): ?\DateTimeImmutable
    {
        return $this->first_date_registration;
    }

    public function setFirstDateRegistration(\DateTimeImmutable $first_date_registration): static
    {
        $this->first_date_registration = $first_date_registration;

        return $this;
    }

    // public function getMarqueId(): ?int
    // {
    //     return $this->marque_id;
    // }

    // public function setMarqueId(int $marque_id): static
    // {
    //     $this->marque_id = $marque_id;

    //     return $this;
    // }
    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(Marque $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
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
}
