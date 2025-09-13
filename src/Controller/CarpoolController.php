<?php

namespace App\Controller;

use App\Entity\Carpooling;
use App\Entity\Participation;
use App\Entity\User;
use App\Form\CarpoolType;
use App\Repository\CarpoolingRepository;
use App\Repository\ParticipationRepository;
use App\Repository\UserRepository;
use App\Services\GeolocationService;
use App\Services\SendEmailService;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CarpoolController extends AbstractController
{
    #[Route('/carpool', name: 'app_carpool_index')]
    #[IsGranted('ROLE_DRIVER')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(CarpoolingRepository $carpoolRep, ParticipationRepository $participation): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // if ($user === null) {
        //     $this->addFlash(
        //         'danger',
        //         'Vous ne pouvez pas accéder à cette page tant que vous n\'êtes pas connecté(e).'
        //     );
        //     return $this->redirectToRoute('app_login');
        // }
        //Section Mes Trajets:
        $soonCarpools = $carpoolRep->findAllByUserAndDate($user, 1);
        $currentCarpools = $carpoolRep->findAllByUserAndDate($user, 0);
        $previousCarpools = $carpoolRep->findAllByUserAndDate($user, -1);

        // Ajouter trois nouveaux onglet dans la partie : Trajets rejoins
        // Section Trajets rejoins
        $allParticipation = $participation->findBy(['user' => $user]);

        return $this->render('carpool/index.html.twig', [
            'user' => $user ?? null,
            'soonCarpools' => $soonCarpools,
            'previousCarpools' => $previousCarpools,
            'currentCarpools' => $currentCarpools,
            'allParticipation' => $allParticipation,
        ]);
    }



    #[Route('/carpool/create', name: 'app_carpool_create')]
    public function createCarpool(
        Request $request,
        EntityManagerInterface $em,
        User $user,
        GeolocationService $gs,
    ): Response {
        /** @var User $user  */
        $user = $this->getUser();


        if ($user === null) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas accéder à cette page tant que vous n\'êtes pas connecté.'
            );
            return $this->redirectToRoute('app_login');
        }

        if ($user->getCurrentCar() === null) {
            $this->addFlash(
                'warning',
                'Veuillez ajouter une voiture avant de proposer un trajet.'
            );
            return $this->redirectToRoute('app_car_index');
        }

        $userCar = $user->getCurrentCar();

        $carpooling = new Carpooling();
        $form = $this->createForm(CarpoolType::class, $carpooling);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $carpooling->setCreatedBy($user);
            $carpooling->setCar($userCar);
            $carpooling->setStartPlace($gs->getOfficialCityName($carpooling->getStartPlace()));
            $carpooling->setEndPlace($gs->getOfficialCityName($carpooling->getEndPlace()));

            //calcul de la durée
            $duration = $gs->routeTimeCalcul($carpooling->getStartPlace(), $carpooling->getEndPlace());
            $newdate = $carpooling->getStartDate();

            $interval = new DateInterval('PT' . $duration . 'M');
            $newdate = $newdate->add($interval);

            //On met à jour le carpool
            $carpooling->setEndDate(new DateTimeImmutable($newdate->format('Y-m-d H:i')));
            $carpooling->setStatut('Online');
            $em->persist($carpooling);

            //On retire 2 crédits comme indiqué dans la demande client
            $user->addEcopiece(-2);
            $em->persist($user);

            $em->flush();
            $this->addFlash('success', 'Votre trajet à bien été mise en ligne !');
            return $this->redirectToRoute('app_carpool_index');
        }

        return $this->render('carpool/create.html.twig', [
            'user' => $user ?? null,
            'form' => $form->createView(),
            'userCar' => $userCar ?? null
        ]);
    }

    #[Route('/carpool/delete/{id}', name: 'app_carpool_delete', requirements: ['id' => '\d+'])]
    public function deleteCarpool(
        EntityManagerInterface $em,
        int $id,
        CarpoolingRepository $carpoolRep,
        ParticipationRepository $partipRep,
        SendEmailService $mail
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        if ($user === null) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas accéder à cette page tant que vous n\'êtes pas connecté(e).'
            );
            return $this->redirectToRoute('app_login');
        }

        /** @var Carpooling $carpooling */
        $carpooling = $carpoolRep->find($id);

        if ($carpooling->getCreatedBy() !== $user) {
            $this->addFlash(
                'danger',
                'Le trajet ne peut pas être supprimé, si le problème persiste, contactez l\'administrateur.'
            );
            return $this->redirectToRoute('app_carpool_index');
        }

        $allParticipation = $partipRep->findBy(['carpooling' => $carpooling]);
        if (!empty($allParticipation)) {
            foreach ($allParticipation as $participation) {
                /** @var User $impactedUser */
                $impactedUser = $participation->getUser();

                //On rembourse l'user impacté par l'annulation, et on envoie un mail pour prévnir de l'annulation du trajet.
                $impactedUser->addEcopiece($carpooling->getPricePerPerson());
                $mail->send(
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
        $em->persist($user);

        $em->remove($carpooling);
        $em->flush();
        $this->addFlash(
            'success',
            'Le trajet à bien été supprimé ! Des mails ont été envoyés aux utilisateurs concernés.'
        );
        return $this->redirectToRoute('app_carpool_index');
    }

    #[Route('/mycarpool/details/{id}', name: 'app_carpool_details', requirements: ['id' => '\d+'])]
    public function detailsMyCarpool(int $id, CarpoolingRepository $carpoolRep, ParticipationRepository $participationRep): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user === null) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas accéder à cette page tant que vous n\'êtes pas connecté(e).'
            );
            return $this->redirectToRoute('app_login');
        }

        $carpool = $carpoolRep->find($id);
        $allParticipation = $participationRep->findBy(['carpooling' => $carpool]);
        return $this->render('carpool/detail.html.twig', [
            'user' => $user,
            'carpool' => $carpool,
            'allParticipation' => $allParticipation
        ]);
    }

    #[Route('mycarpool/{carpool_id}/kickuser/{user_id}', name: 'app_mycarpool_kick_user', requirements: ['carpool_id' => '\d+', 'user_id' => '\d+'])]
    public function kickUserFromCarpool(
        int $carpool_id,
        int $user_id,
        CarpoolingRepository $carpoolRep,
        ParticipationRepository $partipRep,
        UserRepository $userRep,
        EntityManagerInterface $em,
        SendEmailService $sendEmail,
    ) {

        $user = $this->getUser();
        if ($user === null) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas accéder à cette page tant que vous n\'êtes pas connecté(e).'
            );
            return $this->redirectToRoute('app_login');
        }
        /** @var Carpooling $carpool */
        $carpool = $carpoolRep->find($carpool_id);

        /** @var User $kickedUser */
        $kickedUser = $userRep->find($user_id);

        /** @var Participation $participation */
        $participation = $partipRep->findOneBy(['user' => $kickedUser, 'carpooling' => $carpool]);

        if ($carpool->getCreatedBy() !== $user || $carpool->getStatut() !== 'Online' || $carpool->getStartDate() < new DateTimeImmutable('-2 hours') || $participation === null) {
            $this->addFlash(
                'danger',
                'Un problème est survenue avec le lien, si le problème persiste contact l\'administrateur.'
            );
            return $this->redirectToRoute('app_carpool_index');
        }

        //Après toutes les vérifications (sécurité), on supprime la participation du User
        $em->remove($participation);


        //On rembourse le User
        $kickedUser->addEcopiece($carpool->getPricePerPerson());
        $em->persist($kickedUser);


        //On remet à jour le nombre de siège sur le Carpool
        $carpool->addAvailableSeat(1);
        $em->persist($carpool);
        $em->flush();

        //On envoie un mail au User expulsé de la participation pour le prévenir.
        $sendEmail->send(
            'no-reply@ecoride-project.test',
            $kickedUser->getEmail(),
            "IMPORTANT - l'hôte d'un de vos trajets prévus vous a expulsé de sa course",
            'kick_carpool',
            compact('carpool', 'user', 'kickedUser')
        );

        //On recharge la page de détail du User
        $this->addFlash(
            'success',
            "La participation de l'utilisateur " . $kickedUser->getUsername() . " a bien été supprimé, un mail lui a été addressé pour le prévenir."
        );
        return $this->redirectToRoute('app_carpool_index');
    }

    #[Route('joinedcarpool/cancel/{id}', name: 'app_joinedcarpool_cancel_user', requirements: ['id' => '\d+'])]
    public function cancelParticipationCarpool(
        int $id,
        CarpoolingRepository $carpoolRep,
        ParticipationRepository $partipRep,
        EntityManagerInterface $em,
    ) {
        //Annuler la participation d'un participant (règle métier : faisable au maximume 2 h avant le début de la course)
        //Vérification : 
        //A faire : 
        // Supprimer la participation User Carpooling correspondant.
        // Rembourser les pièces correspondant au prix de la course
        // Ajouter une place disponible à la course

        /** @var User $user */
        $user = $this->getUser();
        if ($user === null) {
            $this->addFlash(
                'danger',
                "Vous ne pouvez pas accéder à cette page tant que vous n'êtes pas connecté(e)."
            );
            return $this->redirectToRoute('app_login');
        }

        /** @var Carpooling $carpool */
        $carpool = $carpoolRep->find($id);
        if ($carpool === null) {
            $this->addFlash(
                'danger',
                'Un problème est survenue avec le lien, si le problème persiste contactez l\'administrateur.'
            );
            return $this->redirectToRoute('app_carpool_index');
        }

        $participation = $partipRep->findOneBy(['user' => $user, 'carpooling' => $carpool]);
        if ($participation === null) {
            $this->addFlash(
                'danger',
                'Un problème est survenue avec le lien, si le problème persiste contactez l\'administrateur.'
            );
            return $this->redirectToRoute('app_carpool_index');
        }

        $checkDate = $carpool->getStartDate()->modify('-2 hours');
        if ($checkDate <= new DateTimeImmutable()) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas annuler cette participation. Une participation peut être annulée au maximum deux heures avant le début du trajet.'
            );
            return $this->redirectToRoute('app_carpool_index');
        }
        if ($carpool->getStartDate() <= new DateTimeImmutable()) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas annuler une participation à un précédent trajet.'
            );
            return $this->redirectToRoute('app_carpool_index');
        }
        //Rembourse au user le trajet en écopièce:
        $user->addEcopiece($carpool->getPricePerPerson());
        $carpool->addAvailableSeat(1);

        $em->persist($user);
        $em->persist($carpool);
        $em->remove($participation);

        $em->flush();
        $this->addFlash(
            'success',
            'La participation au trajet ' . $carpool->getStartPlace() . ' > ' . $carpool->getEndPlace() . ' a bien été pris en compte ! Un remboursement de ' . $carpool->getPricePerPerson() . ' écopièces a été effectué.'
        );
        return $this->redirectToRoute('app_carpool_index');
    }
}
