<?php

namespace App\Services;

use App\Entity\Car;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class CarManagerService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function FinalizeCreate(User $user, Car $car, bool $edit = false): static
    {
        if (!$edit) {
            $car->setUserId($user->getId());
            $user->setCurrentCar($car);
        }

        $this->em->flush();
        return $this;
    }

    public function FinalizeSetDefaultCar(User $user, ?Car $car): static
    {
        $user->setCurrentCar($car);
        $this->em->persist($user);
        $this->em->flush();
        return $this;
    }

    public function FinalizeDelation(User $user, Car $car): static
    {
        $car->setUserId(null);
        $user->setCurrentCar(null);

        $this->em->persist($car);
        $this->em->persist($user);
        $this->em->flush();
        return $this;
    }
}
