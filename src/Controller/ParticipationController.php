<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Carpooling;
use App\Entity\Participation;
use App\Entity\User;
use App\Repository\CarpoolingRepository;
use App\Repository\ParticipationRepository;
use App\Repository\UserRepository;
use App\Services\ParticipationManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ParticipationController extends AbstractController
{
    #[Route('/carpool/{id}/participate', name: 'app_carpool_participate', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[IsGranted('ROLE_PASSAGER')]
    public function participateToCarpool(int $id, CarpoolingRepository $carpoolingRep, ParticipationRepository $participationRep, ParticipationManagerService $participationManager)
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Carpooling $carpool */
        $carpool = $carpoolingRep->find($id);

        // Vérification de chaque élément sujet à comprommetre l'acceptation au covoiturage. A REFACTO
        if ($carpool->getAvailableSeat() === 0) {
            $this->addFlash(
                'danger',
                'Ce trajet n\'est plus disponible.'
            );
            return $this->redirectToRoute('app_carpool_index');
        }
        if ($user === $carpool->getCreatedBy()) {
            $this->addFlash(
                'danger',
                "Vous ne pouvez pas participé à votre propre covoiturage"
            );
            return $this->redirectToRoute('app_search_carpool');
        }
        if ($carpool->getStatut() !== 'ONLINE') {
            $this->addFlash(
                'danger',
                "Ce trajet n'est plus disponible"
            );
            return $this->redirectToRoute('app_search_carpool');
        }
        if ($participationRep->findOneBy(['user' => $user, 'carpooling' => $carpool]) !== null) {
            $this->addFlash(
                'danger',
                "Vous participez déjà à ce covoiturage."
            );
            return $this->redirectToRoute('app_search_carpool');
        }
        if ($carpool->getPricePerPerson() > $user->getEcopiece()) {
            $this->addFlash(
                'danger',
                "Vous n'avez pas assez d\'écopièces ! (Écopièces nécessaire : " . $carpool->getPricePerPerson() . ".)"
            );
            return $this->redirectToRoute('app_search_carpool_detail', ['id' => $id]);
        }

        $participationManager->FinalizeParticipation($user, $carpool);

        $cityA = $carpool->getStartPlace();
        $cityB = $carpool->getEndPlace();
        $this->addFlash(
            'success',
            "La participation au trajet $cityA > $cityB a bien été prise en compte !"
        );
        return $this->redirectToRoute('app_carpool_index');
    }

    #[Route('joinedcarpool/cancel/{id}', name: 'app_joinedcarpool_cancel_user', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted(['IS_AUTHENTICATED_FULLY', 'ROLE_DRIVER'])]
    public function cancelParticipationCarpool(int $id, CarpoolingRepository $carpoolRep, ParticipationRepository $partipRep, ParticipationManagerService $participationManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

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
        $participationManager->FinalizeCancel($user, $participation, $carpool);

        $this->addFlash(
            'success',
            'La participation au trajet ' . $carpool->getStartPlace() . ' > ' . $carpool->getEndPlace() . ' a bien été annulé ! Un remboursement de '
                . $carpool->getPricePerPerson() . ' écopièces a été effectué.'
        );
        return $this->redirectToRoute('app_carpool_index');
    }

    #[Route('mycarpool/{carpool_id}/kickuser/{user_id}', name: 'app_mycarpool_kick_user', requirements: ['carpool_id' => '\d+', 'user_id' => '\d+'], methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY', 'ROLE_DRIVER')]
    public function kickUserFromCarpool(int $carpool_id, int $user_id, CarpoolingRepository $carpoolRep, ParticipationRepository $partipRep, UserRepository $userRep, ParticipationManagerService $participationManager)
    {

        $user = $this->getUser();

        /** @var Carpooling $carpool */
        $carpool = $carpoolRep->find($carpool_id);

        /** @var User $kickedUser */
        $kickedUser = $userRep->find($user_id);

        /** @var Participation $participation */
        $participation = $partipRep->findOneBy(['user' => $kickedUser, 'carpooling' => $carpool]);
        if ($carpool->getCreatedBy() !== $user || $carpool->getStatut() !== 'ONLINE' || !$carpool->isCancelable() || $participation === null) {
            $this->addFlash(
                'danger',
                'Un problème est survenue avec le lien, si le problème persiste contact l\'administrateur.'
            );
            return $this->redirectToRoute('app_carpool_index');
        }

        $participationManager->FinalizeKickUser($user, $kickedUser, $participation, $carpool);

        //On recharge la page de détail du User
        $this->addFlash(
            'success',
            "La participation de l'utilisateur " . $kickedUser->getUsername() . " a bien été supprimé, un mail lui a été addressé pour le prévenir."
        );
        return $this->redirectToRoute('app_carpool_index');
    }
}
