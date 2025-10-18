<?php

namespace App\Controller;

use App\Entity\Carpooling;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\CarpoolingRepository;
use App\Repository\ParticipationRepository;
use App\Repository\UserRepository;
use App\Repository\UserReviewRepository;
use App\Services\ReviewManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ReviewController extends AbstractController
{
    #[Route('/review/{user_id}/add/{carpool_id}', name: 'app_review', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PASSAGER')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function addReview(
        int $carpool_id,
        int $user_id,
        CarpoolingRepository $carpoolingRepository,
        UserRepository $userRepository,
        ParticipationRepository $participationRepository,
        Request $request,
        ReviewManagerService $reviewManager,
        UserReviewRepository $userReviewRepository
    ): Response {
        $user = $this->getUser();
        /** @var Carpooling $carpooling */
        $carpooling = $carpoolingRepository->find($carpool_id);;
        $driverUser = $carpooling->getCreatedBy();
        if (
            $user !== $userRepository->find($user_id) ||
            empty($participationRepository->findBy(['carpooling' => $carpooling, 'user' => $user])) ||
            $carpooling->getStatut() !== 'DONE'
        ) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas accéder à cette page.'
            );
            return $this->redirectToRoute('home');
        }

        if (!empty($userReviewRepository->findBy(['user' => $user, 'carpooling' => $carpooling]))) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas donner deux avis sur une même course.'
            );
            return $this->redirectToRoute('home');
        }

        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Review $review */
            $review = $form->getData();
            $reviewManager->publishReview($review, $user, $driverUser, $carpooling, $form->get('grade')->getData());
            $this->addFlash(
                'success',
                'Votre avis a bien été publié !'
            );
            return $this->redirectToRoute('home');
        }
        return $this->render('review/index.html.twig', [
            'form' => $form,
            'user' => $user,
            'driverUser' => $driverUser,
            'carpooling' => $carpooling
        ]);
    }
}
