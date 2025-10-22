<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail("admin")
            ->setPassword(password_hash('azerty', PASSWORD_BCRYPT))
            ->setRoles(['ROLE_PASSAGER', 'ROLE_DRIVER', 'ROLE_ADMIN'])
            ->setFirstName("Admin")
            ->setLastName("Admin")
            ->setPhoneNumber("N/A")
            ->setIsVerified(true)
            ->setUsername("Admin")
            ->setPostalAdress("N/A")
            ->setGrade(5)
            ->setIsVerified(true);
        $manager->persist($admin);
        for ($i = 0; $i < 15; $i++) {
            $user = new User();
            $user->setEmail("fixture$i@mail.com")
                ->setPassword(password_hash('azerty', PASSWORD_BCRYPT))
                ->setRoles(['ROLE_PASSAGER', 'ROLE_DRIVER'])
                ->setFirstName("FirstName$i")
                ->setLastName("LastName$i")
                ->setPhoneNumber("060102030$i")
                ->setIsVerified(true)
                ->setUsername("fixture$i")
                ->setPostalAdress("1234 Fixture St, City $i")
                ->setGrade($i)
                ->setIsVerified(true);
            $manager->persist($user);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
