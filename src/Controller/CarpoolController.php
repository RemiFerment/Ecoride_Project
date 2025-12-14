<?php

namespace App\Controller;

use App\Entity\Carpooling;
use App\Entity\User;
use App\Form\CarpoolType;
use App\Repository\CarpoolingRepository;
use App\Repository\ParticipationRepository;
use App\Repository\UserReviewRepository;
use App\Security\Voter\CarpoolVoter;
use App\Services\CarpoolManagerService;
use App\Services\ParticipationManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CarpoolController extends AbstractController
{
    private ?User $user;

    public function __construct(private Security $security)
    {
        $this->user = $this->security->getUser();
    }

    #[Route('/carpool', name: 'app_carpool_index', methods: ['GET'])]
    #[IsGranted(CarpoolVoter::READ)]
    public function index(CarpoolingRepository $carpoolRep, ParticipationRepository $participation): Response
    {
        $onlineCarpools = $carpoolRep->findAllByUserAndStatut($this->user, 'ONLINE');
        $inProgressCarpools = $carpoolRep->findAllByUserAndStatut($this->user, 'IN_PROGRESS');
        $doneCarpools = $carpoolRep->findAllByUserAndStatut($this->user, 'DONE');
        $missCarpools = $carpoolRep->findAllByUserAndStatut($this->user, 'MISS');

        // Ajouter trois nouveaux onglet dans la partie : Trajets rejoins
        // Section Trajets rejoins
        $allParticipation = $participation->findBy(['user' => $this->user]);

        return $this->render('carpool/index.html.twig', [
            'onlineCarpools' => $onlineCarpools,
            'inProgressCarpools' => $inProgressCarpools,
            'doneCarpools' => $doneCarpools,
            'missCarpools' => $missCarpools,
            'allParticipation' => $allParticipation,
        ]);
    }

    #[Route('/carpool/create', name: 'app_carpool_create', methods: ['GET', 'POST'])]
    #[IsGranted(CarpoolVoter::CREATE)]
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
    #[IsGranted(CarpoolVoter::DELETE, subject: 'carpooling')]
    public function deleteCarpool(Carpooling $carpooling, ParticipationRepository $partipRep, CarpoolManagerService $carpoolManager): Response
    {
        $allParticipation = $partipRep->findBy(['carpooling' => $carpooling]);
        $carpoolManager->FinalizeDeletion($carpooling, $allParticipation, $this->user);

        $this->addFlash(
            'success',
            "Le trajet " .  $carpooling->getStartPlace() . " → " . $carpooling->getEndPlace() . " du " . $carpooling->getStartDate()->format('d/m/Y') . " à bien été supprimé ! Des mails ont été envoyés aux utilisateurs concernés."
        );
        return $this->redirectToRoute('app_carpool_index');
    }

    #[Route('/mycarpool/details/{id}', name: 'app_carpool_details', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted(CarpoolVoter::READ, subject: 'carpooling')]
    public function detailsMyCarpool(Carpooling $carpooling, ParticipationRepository $participationRep, UserReviewRepository $userReviewRepository): Response
    {
        $allParticipation = $participationRep->findBy(['carpooling' => $carpooling]);
        $carpoolReviews = $userReviewRepository->findByChecked($carpooling->getId());
        // dd($carpooling);
        return $this->render('carpool/detail.html.twig', [
            'carpool' => $carpooling,
            'allParticipation' => $allParticipation,
            'carpoolReviews' => $carpoolReviews
        ]);
    }

    #[Route('/mycarpool/launch/{id}', name: 'app_carpool_launch', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted(CarpoolVoter::START, subject: 'carpooling')]
    public function launchCarpool(Carpooling $carpooling, CarpoolManagerService $carpoolManager): Response
    {
        $carpoolManager->changeCarpoolStatut($carpooling, 'IN_PROGRESS');

        $this->addFlash(
            'success',
            'Le trajet a bien démarrer, une fois arrivé n\'oubliez pas de l\'indiquer sur l\'application ! Bonne route !'
        );
        return $this->redirectToRoute('app_carpool_details', ['id' => $carpooling->getId()]);
    }

    #[Route('/mycarpool/finalize/{id}', name: 'app_carpool_finalize', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted(CarpoolVoter::END, subject: 'carpooling')]
    public function finalizeCarpool(Carpooling $carpooling, CarpoolManagerService $carpoolManager, ParticipationManagerService $participationManager, ParticipationRepository $participationRep): Response
    {

        $allParticipation = $participationRep->findBy(['carpooling' => $carpooling]);
        $carpoolManager->ChangeCarpoolStatut($carpooling, 'DONE');
        $participationManager->FinalizeCarpool($this->user, $carpooling, $allParticipation);
        $this->addFlash(
            'success',
            'Le trajet a bien été finalisé, un mail à été envoyé au participant pour noter et donner leur ressenti sur le trajet. Vous avez été crédité de ' . count($allParticipation) * $carpooling->getPricePerPerson() . ' écopièces.'
        );
        return $this->redirectToRoute('app_carpool_index');
    }
}
