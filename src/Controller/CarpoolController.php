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
    #[IsGranted('ROLE_DRIVER')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function createCarpool(
        Request $request,
        EntityManagerInterface $em,
        User $user,
        GeolocationService $gs,
    ): Response {
        /** @var User $user  */
        $user = $this->getUser();

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
    #[IsGranted('ROLE_DRIVER')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteCarpool(
        EntityManagerInterface $em,
        int $id,
        CarpoolingRepository $carpoolRep,
        ParticipationRepository $partipRep,
        SendEmailService $mail
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

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
    #[IsGranted('ROLE_DRIVER')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function detailsMyCarpool(int $id, CarpoolingRepository $carpoolRep, ParticipationRepository $participationRep): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $carpool = $carpoolRep->find($id);
        $allParticipation = $participationRep->findBy(['carpooling' => $carpool]);
        return $this->render('carpool/detail.html.twig', [
            'user' => $user,
            'carpool' => $carpool,
            'allParticipation' => $allParticipation
        ]);
    }
}
