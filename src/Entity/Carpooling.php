<?php

namespace App\Entity;

use App\Repository\CarpoolingRepository;
use App\Validator\CityCheck;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarpoolingRepository::class)]
class Carpooling
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotBlank(message: "Veuillez sélectionner une date.")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La date doit être aujourd'hui ou ultérieure."
    )]
    private ?\DateTimeImmutable $start_date = null;
    #[ORM\Column(length: 255)]
    #[CityCheck()]
    #[NotBlank()]
    private ?string $start_place = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $end_date = null;


    #[ORM\Column(length: 255)]
    #[CityCheck()]
    #[NotBlank()]
    private ?string $end_place = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?int $available_seat = null;

    #[ORM\Column]
    private ?int $price_per_person = null;

    #[ORM\ManyToOne(targetEntity: Car::class)]
    private ?Car $car = null;


    #[ORM\ManyToOne(inversedBy: 'carpoolings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $created_by = null;

    /**
     * @var Collection<int, Participation>
     */
    #[ORM\OneToMany(targetEntity: Participation::class, mappedBy: 'carpooling')]
    private Collection $participations;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }

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

    public function getAvailableSeat(): ?int
    {
        return $this->available_seat;
    }

    public function setAvailableSeat(int $available_seat): static
    {
        $this->available_seat = $available_seat;

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

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->created_by;
    }

    public function setCreatedBy(?User $created_by): static
    {
        $this->created_by = $created_by;

        return $this;
    }

    /**
     * @return Collection<int, Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): static
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setCarpooling($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): static
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getCarpooling() === $this) {
                $participation->setCarpooling(null);
            }
        }

        return $this;
    }
}
