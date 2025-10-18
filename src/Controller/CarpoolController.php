<?php

namespace App\Controller;

use App\Entity\Carpooling;
use App\Entity\User;
use App\Form\CarpoolType;
use App\Repository\CarpoolingRepository;
use App\Repository\ParticipationRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserReviewRepository;
use App\Services\CarpoolManagerService;
use App\Services\GlobalStatManager;
use App\Services\ParticipationManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CarpoolController extends AbstractController
{
    #[Route('/carpool', name: 'app_carpool_index', methods: ['GET'])]
    #[IsGranted('ROLE_DRIVER')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(CarpoolingRepository $carpoolRep, ParticipationRepository $participation): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $onlineCarpools = $carpoolRep->findAllByUserAndStatut($user, 'ONLINE');
        $inProgressCarpools = $carpoolRep->findAllByUserAndStatut($user, 'IN_PROGRESS');
        $doneCarpools = $carpoolRep->findAllByUserAndStatut($user, 'DONE');
        $missCarpools = $carpoolRep->findAllByUserAndStatut($user, 'MISS');

        // Ajouter trois nouveaux onglet dans la partie : Trajets rejoins
        // Section Trajets rejoins
        $allParticipation = $participation->findBy(['user' => $user]);

        return $this->render('carpool/index.html.twig', [
            'user' => $user,
            'onlineCarpools' => $onlineCarpools,
            'inProgressCarpools' => $inProgressCarpools,
            'doneCarpools' => $doneCarpools,
            'missCarpools' => $missCarpools,
            'allParticipation' => $allParticipation,
        ]);
    }



    #[Route('/carpool/create', name: 'app_carpool_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DRIVER')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function createCarpool(Request $request, CarpoolManagerService $carpool_manager): Response
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
            $carpool_manager->FinalizeCreation($carpooling, $user);
            $this->addFlash('success', 'Votre trajet à bien été mise en ligne !');
            return $this->redirectToRoute('app_carpool_index');
        }

        return $this->render('carpool/create.html.twig', [
            'user' => $user ?? null,
            'form' => $form->createView(),
            'userCar' => $userCar ?? null
        ]);
    }

    #[Route('/carpool/delete/{id}', name: 'app_carpool_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted('ROLE_DRIVER')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteCarpool(int $id, CarpoolingRepository $carpoolRep, ParticipationRepository $partipRep, CarpoolManagerService $carpoolManager): Response
    {
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

    #[Route('/mycarpool/details/{id}', name: 'app_carpool_details', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_DRIVER')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function detailsMyCarpool(
        int $id,
        CarpoolingRepository $carpoolRep,
        ParticipationRepository $participationRep,
        UserReviewRepository $userReviewRepository,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $carpool = $carpoolRep->find($id);
        $allParticipation = $participationRep->findBy(['carpooling' => $carpool]);
        $carpoolReviews = $userReviewRepository->findBy(['carpooling' => $carpool]);
        return $this->render('carpool/detail.html.twig', [
            'user' => $user,
            'carpool' => $carpool,
            'allParticipation' => $allParticipation,
            'carpoolReviews' => $carpoolReviews
        ]);
    }

    #[Route('/mycarpool/launch/{id}', name: 'app_carpool_launch', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function launchCarpool(int $id, CarpoolingRepository $carpoolRep, CarpoolManagerService $carpoolManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Carpooling $carpool */
        $carpool = $carpoolRep->find($id);
        // dd(!$carpool->checkUser($user) || !$carpool->isLaunchable());
        if (!$carpool->checkUser($user) || !$carpool->isLaunchable()) {
            $this->addFlash(
                'danger',
                'Le trajet ne peut pas être démarrer., si le problème persiste, contactez l\'administrateur.'
            );
            return $this->redirectToRoute('app_carpool_index');
        }
        if ($carpool->getStatut() !== 'ONLINE') {
            $this->addFlash(
                'danger',
                'Le trajet ne peut pas être démarrer., si le problème persiste, contactez l\'administrateur.'
            );
            return $this->redirectToRoute('app_carpool_details', ['id' => $id]);
        }
        if (!$carpool->getStatut() === 'IN_PROGRESS') {
            $this->addFlash(
                'danger',
                'Le trajet est déjà en cours.'
            );
            return $this->redirectToRoute('app_carpool_details', ['id' => $id]);
        }

        $carpoolManager->changeCarpoolStatut($carpool, 'IN_PROGRESS');

        $this->addFlash(
            'success',
            'Le trajet a bien démarrer, une fois arrivé n\'oubliez pas de l\'indiquer sur l\'application ! Bonne route !'
        );
        return $this->redirectToRoute('app_carpool_details', ['id' => $id]);
    }

    #[Route('/mycarpool/finalize/{id}', name: 'app_carpool_finalize', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function finalizeCarpool(int $id, CarpoolingRepository $carpoolRep, CarpoolManagerService $carpoolManager, ParticipationManagerService $participationManager, ParticipationRepository $participationRep): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Carpooling $carpool */
        $carpool = $carpoolRep->find($id);

        if (!$carpool->checkUser($user)) {
            $this->addFlash(
                'danger',
                'Le trajet ne peut pas être démarrer., si le problème persiste, contactez l\'administrateur.'
            );
            return $this->redirectToRoute('app_carpool_index');
        }
        $allParticipation = $participationRep->findBy(['carpooling' => $carpool]);
        $carpoolManager->ChangeCarpoolStatut($carpool, 'DONE');
        $participationManager->FinalizeCarpool($user, $carpool, $allParticipation);
        $this->addFlash(
            'success',
            'Le trajet a bien été finalisé, un mail à été envoyé au participant pour noter et donner leur ressenti sur le trajet. Vous avez été crédité de ' . count($allParticipation) * $carpool->getPricePerPerson() . ' écopièces.'
        );
        return $this->redirectToRoute('app_carpool_index');
    }
}
