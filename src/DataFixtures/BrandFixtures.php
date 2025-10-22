<?php

namespace App\DataFixtures;

use App\Entity\Marque;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brands = [
            'Toyota',
            'Volkswagen',
            'Ford',
            'Honda',
            'Chevrolet',
            'Mercedes-Benz',
            'BMW',
            'Audi',
            'Hyundai',
            'Nissan',
            'Kia',
            'Peugeot',
            'Renault',
            'Fiat',
            'Opel',
            'Mazda',
            'Volvo',
            'Jaguar',
            'Land Rover',
            'Porsche',
            'Ferrari',
            'Lamborghini',
            'Maserati',
            'Bentley',
            'Rolls-Royce',
            'Jeep',
            'Subaru',
            'Mitsubishi',
            'Citroën',
            'Suzuki',
        ];

        foreach ($brands as $brandName) {
            $brand = new Marque();
            $brand->setName($brandName);
            $manager->persist($brand);
        }

        $manager->flush();
    }
}
