<?php

namespace App\Controller;

use App\Entity\Carpooling;
use App\Entity\Review;
use App\Entity\User;
use App\Form\ReviewType;
use App\Security\Voter\ReviewVoter;
use App\Services\ReviewManagerService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ReviewController extends AbstractController
{
    private ?User $user;
    public function __construct(private Security $security)
    {
        $this->user = $this->security->getUser();
    }

    #[Route('/review/{user_id}/add/{carpooling_id}', name: 'app_review', methods: ['GET', 'POST'])]
    #[IsGranted(ReviewVoter::ADD, subject: 'carpooling')]
    public function addReview(#[MapEntity(mapping: ['user_id' => 'id'])] User $user, #[MapEntity(mapping: ['carpooling_id' => 'id'])] Carpooling $carpooling, Request $request, ReviewManagerService $reviewManager,): Response
    {
        $user = $this->user;
        $driverUser = $carpooling->getCreatedBy();

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
            'driverUser' => $driverUser,
            'carpooling' => $carpooling
        ]);
    }
}
