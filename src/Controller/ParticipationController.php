<?php

namespace App\Controller;

use App\Repository\CarpoolingRepository;
use App\Repository\ParticipationRepository;
use App\Repository\UserRepository;
use App\Services\SendEmailService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ParticipationController extends AbstractController
{
    #[Route('joinedcarpool/cancel/{id}', name: 'app_joinedcarpool_cancel_user', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_DRIVER')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function cancelParticipationCarpool(
        int $id,
        CarpoolingRepository $carpoolRep,
        ParticipationRepository $partipRep,
        EntityManagerInterface $em,
    ) {
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

    #[Route('mycarpool/{carpool_id}/kickuser/{user_id}', name: 'app_mycarpool_kick_user', requirements: ['carpool_id' => '\d+', 'user_id' => '\d+'])]
    #[IsGranted('ROLE_DRIVER')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
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
}
