<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Carpooling;
use App\Entity\Participation;
use App\Entity\User;
use App\Repository\CarpoolingRepository;
use App\Repository\ParticipationRepository;
use App\Repository\UserRepository;
use App\Security\Voter\ParticipationVoter;
use App\Services\ParticipationManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ParticipationController extends AbstractController
{
    private User $user;

    public function __construct(private Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/carpool/{id}/participate', name: 'app_carpool_participate', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted(ParticipationVoter::JOIN, subject: 'carpooling')]
    public function participateToCarpool(Carpooling $carpooling, ParticipationManagerService $participationManager)
    {
        $participationManager->FinalizeParticipation($this->user, $carpooling);

        $this->addFlash(
            'success',
            "La participation au trajet " . $carpooling->getStartPlace() . " > " . $carpooling->getEndPlace() . " a bien été prise en compte !"
        );
        return $this->redirectToRoute('app_carpool_index');
    }

    #[Route('joinedcarpool/cancel/{id}', name: 'app_joinedcarpool_cancel_user', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted(ParticipationVoter::LEAVE, subject: 'carpooling')]
    public function cancelParticipationCarpool(Carpooling $carpooling, ParticipationRepository $partipRep, ParticipationManagerService $participationManager): Response
    {
        $participation = $partipRep->findOneBy(['user' => $this->user, 'carpooling' => $carpooling]);

        $participationManager->FinalizeCancel($this->user, $participation, $carpooling);

        $this->addFlash(
            'success',
            'La participation au trajet ' . $carpooling->getStartPlace() . ' > ' . $carpooling->getEndPlace() . ' a bien été annulé ! Un remboursement de '
                . $carpooling->getPricePerPerson() . ' écopièces a été effectué.'
        );
        return $this->redirectToRoute('app_carpool_index');
    }

    #[Route('mycarpool/{carpool_id}/kickuser/{user_id}', name: 'app_mycarpool_kick_user', requirements: ['carpool_id' => '\d+', 'user_id' => '\d+'], methods: ['POST'])]
    #[IsGranted(ParticipationVoter::KICK, subject: 'carpooling')]
    public function kickUserFromCarpool(Carpooling $carpooling, int $user_id, ParticipationRepository $partipRep, UserRepository $userRep, ParticipationManagerService $participationManager)
    {
        /** @var User $kickedUser */
        $kickedUser = $userRep->find($user_id);

        /** @var Participation $participation */
        $participation = $partipRep->findOneBy(['user' => $kickedUser, 'carpooling' => $carpooling]);

        $participationManager->FinalizeKickUser($this->user, $kickedUser, $participation, $carpooling);

        //On recharge la page de détail du User
        $this->addFlash(
            'success',
            "La participation de l'utilisateur " . $kickedUser->getUsername() . " a bien été supprimé, un mail lui a été addressé pour le prévenir."
        );
        return $this->redirectToRoute('app_carpool_index');
    }
}
