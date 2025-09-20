<?php

namespace App\Services;

use App\Entity\Carpooling;
use App\Entity\Participation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class ParticipationManagerService
{
    public function __construct(private EntityManagerInterface $em, private SendEmailService $mail) {}

    public function FinalizeParticipation(User $user, Carpooling $carpooling): static
    {
        //On créer la participation et on met à jour les différentes entités
        $participation = new Participation();
        $participation->setUser($user)
            ->setCarpooling($carpooling);
        $user->addEcopiece(-$carpooling->getPricePerPerson());
        $carpooling->addAvailableSeat(-1);

        $this->em->persist($participation);
        $this->em->persist($user);
        $this->em->persist($carpooling);

        $this->em->flush();
        return $this;
    }

    public function FinalizeCancel(User $user, Participation $participation, Carpooling $carpooling): static
    {
        //Rembourse au user le trajet en écopièce:
        $user->addEcopiece($carpooling->getPricePerPerson());
        $carpooling->addAvailableSeat(1);

        $this->em->persist($user);
        $this->em->persist($carpooling);
        $this->em->remove($participation);

        $this->em->flush();
        return $this;
    }

    public function FinalizeKickUser(User $user, User $kickedUser, Participation $participation, Carpooling $carpooling): static
    {
        $this->em->remove($participation);


        //On rembourse le User
        $kickedUser->addEcopiece($carpooling->getPricePerPerson());
        $this->em->persist($kickedUser);


        //On remet à jour le nombre de siège sur le Carpool
        $carpooling->addAvailableSeat(1);
        $this->em->persist($carpooling);
        $this->em->flush();

        //On envoie un mail au User expulsé de la participation pour le prévenir.
        $this->mail->send(
            'no-reply@ecoride-project.test',
            $kickedUser->getEmail(),
            "IMPORTANT - l'hôte d'un de vos trajets prévus vous a expulsé de sa course",
            'kick_carpool',
            compact('carpooling', 'user', 'kickedUser')
        );
        return $this;
    }

    public function FinalizeCarpool(User $user, Carpooling $carpooling, array $allParticipation): static
    {
        //Crédité le coursier en fonction du prix de la course et du nombre de participation
        $user->addEcopiece(count($allParticipation) * $carpooling->getPricePerPerson());
        $this->em->persist($user);
        $this->em->flush();
        //On envoie un mail à tous les user concerné pour noter la course
        /** @var Participation $participation */
        if (!empty($allParticipation) || $allParticipation !== null) {
            foreach ($allParticipation as $participation) {
                $contributor = $participation->getUser();
                $this->mail->send(
                    'contact@ecoride.test',
                    $contributor->getEmail(),
                    'Noter dès maintenant votre course',
                    'grade_carpool',
                    compact('contributor', 'carpooling', 'user')
                );
            }
        }
        return $this;
    }
}
