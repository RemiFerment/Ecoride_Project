<?php

namespace App\Controller;

use App\Entity\Carpooling;
use App\Entity\User;
use App\Form\CarpoolType;
use App\Repository\CarpoolingRepository;
use App\Repository\CarRepository;
use App\Repository\UserCarpoolingRepository;
use App\Services\GeolocationService;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CarpoolController extends AbstractController
{
    #[Route('/carpool', name: 'app_carpool_index')]
    public function index(CarpoolingRepository $carpoolRep, CarRepository $carRep): Response
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
        $soonCarpools = $carpoolRep->findAllByUserAndDate($user, 1);
        $currentCarpools = $carpoolRep->findAllByUserAndDate($user, 0);
        $previousCarpools = $carpoolRep->findAllByUserAndDate($user, -1);
        return $this->render('carpool/index.html.twig', [
            'user' => $user ?? null,
            'soonCarpools' => $soonCarpools,
            'previousCarpools' => $previousCarpools,
            'currentCarpools' => $currentCarpools
        ]);
    }

    #[Route('/carpool/create', name: 'app_carpool_create')]
    public function createCarpool(
        Request $request,
        EntityManagerInterface $em,
        User $user,
        GeolocationService $gs,
        CarRepository $carRep
    ): Response {
        /** @var User $user  */
        $user = $this->getUser();


        if ($user === null) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas accéder à cette page tant que vous n\'êtes pas connecté.'
            );
            return $this->redirectToRoute('home');
        }

        if ($user->getCurrentCarId() === null) {
            $this->addFlash(
                'warning',
                'Veuillez ajouter une voiture avant de proposer un trajet.'
            );
            return $this->redirectToRoute('app_car_index');
        }
        /** @var Car $userCar */
        $userCar = $carRep->find($user->getCurrentCarId());

        $carpooling = new Carpooling();
        $form = $this->createForm(CarpoolType::class, $carpooling);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $carpooling->setCreatedBy($user);
            $carpooling->setCar($carRep->find($user->getCurrentCarId()));
            $carpooling->setStartPlace($gs->getOfficialCityName($carpooling->getStartPlace()));
            $carpooling->setEndPlace($gs->getOfficialCityName($carpooling->getEndPlace()));

            //calcul de la durée
            $duration = $gs->routeTimeCalcul($carpooling->getStartPlace(), $carpooling->getEndPlace());
            $newdate = $carpooling->getStartDate();

            $interval = new DateInterval('PT' . $duration . 'M');
            $newdate = $newdate->add($interval);

            $carpooling->setEndDate(new DateTimeImmutable($newdate->format('Y-m-d H:i')));

            $carpooling->setStatut('Online');

            $em->persist($carpooling);
            $em->flush();

            //On retire 2 crédits comme indiqué dans la demande client
            $user->setEcopiece($user->getEcopiece() - 2);
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
    public function deleteCarpool(EntityManagerInterface $em, int $id, CarpoolingRepository $carpoolRep): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Carpooling $carpooling */
        if (!$carpooling = $carpoolRep->find($id)) {
            $this->addFlash(
                'danger',
                'Le trajet ne peut pas être supprimé, si le problème persiste, contactez l\'administrateur réseau.'
            );
            return $this->redirectToRoute('app_carpool_index');
        }
        if ($carpooling->getCreatedBy() !== $user) {
            $this->addFlash(
                'danger',
                'Le trajet ne peut pas être supprimé, si le problème persiste, contactez l\'administrateur réseau.'
            );
            return $this->redirectToRoute('app_carpool_index');
        }
        $em->remove($carpooling);
        $em->flush();

        //Remise des pièces utilisées pour créer le trajet.
        $user->setEcopiece($user->getEcopiece() + 2);
        $em->persist($user);
        $em->flush();
        $this->addFlash(
            'success',
            'Le trajet à bien été supprimé !'
        );
        return $this->redirectToRoute('app_carpool_index');
    }

    #[Route('/carpool/details/{id}', name: 'app_carpool_details', requirements: ['id' => '\d+'])]
    public function detailsCarpool(EntityManagerInterface $em, int $id, CarpoolingRepository $carpoolRep, UserCarpoolingRepository $userCarpoolRep): Response
    {
        $user = $this->getUser();

        $carpool = $carpoolRep->find($id);

        return $this->render('carpool/detail.html.twig', [
            'user' => $user,
            'carpool' => $carpool,
        ]);
    }
}
