<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ModeratorVoter extends Voter
{
    public const CHECK = 'REVIEW_CHECK';
    public const CONFLICT = 'CONFLICT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CHECK, self::CONFLICT])
            && ($subject instanceof \App\Entity\Review || $subject instanceof \App\Entity\UserReview);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::CHECK:
                return $user->hasRole('ROLE_MODERATOR') && $subject->getStatut() === 'TO_BE_CHECKED';
            case self::CONFLICT:
                return $user->hasRole('ROLE_MODERATOR') && !in_array($subject->getStatut(), ['REFUNDED', 'DISMISS']);
                break;
        }

        return false;
    }
}
