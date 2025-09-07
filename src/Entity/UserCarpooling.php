<?php

namespace App\Entity;

use App\Repository\UserCarpoolingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCarpoolingRepository::class)]
class UserCarpooling
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'userCarpoolings')]
    private Collection $user;

    /**
     * @var Collection<int, Carpooling>
     */
    #[ORM\OneToMany(targetEntity: Carpooling::class, mappedBy: 'userCarpooling')]
    private Collection $carpooling;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->carpooling = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Carpooling>
     */
    public function getCarpooling(): Collection
    {
        return $this->carpooling;
    }

    public function addCarpooling(Carpooling $carpooling): static
    {
        if (!$this->carpooling->contains($carpooling)) {
            $this->carpooling->add($carpooling);
            $carpooling->setUserCarpooling($this);
        }

        return $this;
    }

    public function removeCarpooling(Carpooling $carpooling): static
    {
        if ($this->carpooling->removeElement($carpooling)) {
            // set the owning side to null (unless already changed)
            if ($carpooling->getUserCarpooling() === $this) {
                $carpooling->setUserCarpooling(null);
            }
        }

        return $this;
    }
}
