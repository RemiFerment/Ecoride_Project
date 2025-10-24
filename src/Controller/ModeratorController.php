<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use App\Repository\UserReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/moderator')]
final class ModeratorController extends AbstractController
{
    #[Route('/dashboard', name: 'app_moderator', methods: ['GET'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function index(ReviewRepository $reviewRepository, UserReviewRepository $userReviewRepository): Response
    {
        //dashboard dans laquelle il y a deux choix : Un pour vérifié les avis, un pour gérer les 
        //Donc ici, il faut juste que je récupère le nombre d'avis à checker + le nombre d'avis dont la note est inférieur à 3
        $reviewAmount = count($reviewRepository->findByToBeChecked());
        $lowGradeAmount = count($userReviewRepository->findByLowGrade());

        return $this->render('moderator/index.html.twig', [
            'reviewAmount' => $reviewAmount,
            'lowGradeAmount' => $lowGradeAmount,
        ]);
    }

    #[Route('/reviews/list', name: 'app_reviews_list', methods: ['GET'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function reviewsList(UserReviewRepository $userReviewRepository): Response
    {
        $allreviews = $userReviewRepository->findByToBeChecked();

        return $this->render('moderator/review_list.html.twig', [
            'allReviews' => $allreviews
        ]);
    }

    #[Route('/review/validated/{id}', name: 'app_review_validated', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function validatedReview(Review $review, EntityManagerInterface $em)
    {
        $review->setStatut('CHECKED');
        $em->persist($review);
        $em->flush();
        return $this->redirectToRoute('app_reviews_list');
    }
    
    #[Route('/review/dismiss/{id}', name: 'app_review_dismiss', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function dismissReview(Review $review, EntityManagerInterface $em)
    {
        $review->setStatut('DISMISS');
        $em->persist($review);
        $em->flush();
        return $this->redirectToRoute('app_reviews_list');
    }
}
