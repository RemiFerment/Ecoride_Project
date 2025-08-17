<?php

namespace App\Entity;

use App\Repository\CarpoolingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarpoolingRepository::class)]
class Carpooling
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $start_date = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $start_hour = null;

    #[ORM\Column(length: 255)]
    private ?string $start_place = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $end_date = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $end_hour = null;

    #[ORM\Column(length: 255)]
    private ?string $end_place = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?int $avaible_seat = null;

    #[ORM\Column]
    private ?int $price_per_person = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeImmutable $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getStartHour(): ?\DateTimeImmutable
    {
        return $this->start_hour;
    }

    public function setStartHour(\DateTimeImmutable $start_hour): static
    {
        $this->start_hour = $start_hour;

        return $this;
    }

    public function getStartPlace(): ?string
    {
        return $this->start_place;
    }

    public function setStartPlace(string $start_place): static
    {
        $this->start_place = $start_place;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeImmutable $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getEndHour(): ?\DateTimeImmutable
    {
        return $this->end_hour;
    }

    public function setEndHour(\DateTimeImmutable $end_hour): static
    {
        $this->end_hour = $end_hour;

        return $this;
    }

    public function getEndPlace(): ?string
    {
        return $this->end_place;
    }

    public function setEndPlace(string $end_place): static
    {
        $this->end_place = $end_place;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getAvaibleSeat(): ?int
    {
        return $this->avaible_seat;
    }

    public function setAvaibleSeat(int $avaible_seat): static
    {
        $this->avaible_seat = $avaible_seat;

        return $this;
    }

    public function getPricePerPerson(): ?int
    {
        return $this->price_per_person;
    }

    public function setPricePerPerson(int $price_per_person): static
    {
        $this->price_per_person = $price_per_person;

        return $this;
    }
}
