<?php

namespace App\Security\Voter;

use App\Entity\Carpooling;
use App\Entity\User;
use App\Repository\UserReviewRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ReviewVoter extends Voter
{
    public const ADD = 'REVIEW_ADD';

    public function __construct(private UserReviewRepository $userReviewRepository) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::ADD && $subject instanceof Carpooling;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        $alreadyReviewed = $this->userReviewRepository->findOneBy([
            'user' => $user,
            'carpooling' => $subject
        ]);

        return
            $attribute === self::ADD && $user->hasRole('ROLE_PASSAGER') &&
            $subject->getStatut() === 'DONE' &&
            $subject->getParticipations()->exists(function ($key, $participation) use ($user) {
                return $participation->getUser() === $user;
            }) &&
            !$alreadyReviewed;
    }
}
