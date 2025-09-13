<?php

namespace App\Controller;

use App\Entity\Carpooling;
use App\Entity\User;
use App\Form\CarpoolType;
use App\Repository\CarpoolingRepository;
use App\Repository\ParticipationRepository;
use App\Services\CarpoolManagerService;
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
    public function createCarpool(Request $request,CarpoolManagerService $carpool_manager): Response 
    {
        /** @var User $user  */
        $user = $this->getUser();

        if ($user->getCurrentCar() === null) {
            $this->addFlash(
                'warning',
                'Veuillez ajouter une voiture avant de proposer un trajet.'
            );
            return $this->redirectToRoute('app_car_index');
        }


        $carpooling = new Carpooling();
        $form = $this->createForm(CarpoolType::class, $carpooling);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        $carpool_manager->FinalizeCreation($carpooling,$user);
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
        int $id,
        CarpoolingRepository $carpoolRep,
        ParticipationRepository $partipRep,
        CarpoolManagerService $carpoolManager
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

        $carpoolManager->FinalizeDeletion($carpooling, $allParticipation, $user);
        
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
