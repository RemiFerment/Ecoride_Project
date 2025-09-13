<?php
namespace App\Services;

use App\Entity\Carpooling;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class CarpoolManagerService{

    public function __construct(
        private EntityManagerInterface $em,
        private GeolocationService $gs,
        private SendEmailService $mail,
        ) {}

    public function FinalizeCreation(Carpooling $carpool, User $user)
    {
        
        $carpool->setCreatedBy($user)
        ->setCar($user->getCurrentCar())
        ->setStartPlace($this->gs->getOfficialCityName($carpool->getStartPlace()))
        ->setEndPlace($this->gs->getOfficialCityName($carpool->getEndPlace()))
        ->setStatut('ONLINE');
        //Calcul de l'estimation de la durée du trajet via l'API
        $duration = $this->gs->routeTimeCalcul($carpool->getStartPlace(),$carpool->getEndPlace());
        $endDate = $carpool->getStartDate()->modify("+$duration minutes");
        $carpool->setEndDate($endDate);

        //Retire le coup de création d'un trajet au User
        $user->addEcopiece(-2);

        //Persist et flush des deux entités impactés
        $this->em->persist($user);
        $this->em->persist($carpool);
        $this->em->flush();
    }

    public function FinalizeDeletion(Carpooling $carpool, array $allParticipation, User $user)
    {
        
        if (!empty($allParticipation)) {
            foreach ($allParticipation as $participation) {
                /** @var User $impactedUser */
                $impactedUser = $participation->getUser();

                //On rembourse l'user impacté par l'annulation, et on envoie un mail pour prévnir de l'annulation du trajet.
                $impactedUser->addEcopiece($carpool->getPricePerPerson());
                $this->mail->send(
                    'contact@ecoride.test',
                    $impactedUser->getEmail(),
                    'IMPORTANT - Un trajet auquel vous participez a été annulé',
                    'cancel_carpool',
                    compact('impactedUser', 'carpooling', 'user')
                );
            }
        }

        //On rembourse les pièces utilisées pour créer le trajet.
        $user->addEcopiece(2);
        $this->em->persist($user);

        $this->em->remove($carpool);
        $this->em->flush();
    }
}