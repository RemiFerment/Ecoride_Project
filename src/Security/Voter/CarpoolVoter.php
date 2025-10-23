<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class CarpoolVoter extends Voter
{
    public const CREATE = 'CARPOOL_CREATE';
    public const READ = 'CARPOOL_READ';
    public const UPDATE = 'CARPOOL_UPDATE';
    public const DELETE = 'CARPOOL_DELETE';
    public const START = 'CARPOOL_START';
    public const END = 'CARPOOL_END';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::READ || self::CREATE) {
            return true;
        }
        return in_array($attribute, [self::UPDATE, self::DELETE, self::START, self::END])
            && $subject instanceof \App\Entity\Carpooling;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::READ:
            case self::CREATE:
                return true;
            case self::UPDATE:
            case self::DELETE:
                if (!$subject instanceof \App\Entity\Carpooling) {
                    return false;
                }
                return $subject->getCreatedBy()->getId() === $user->getId();
            case self::START:
                if (!$subject instanceof \App\Entity\Carpooling) {
                    return false;
                }
                return in_array('ROLE_DRIVER', $user->getRoles()) && $subject->getCreatedBy()->getId() === $user->getId() && $subject->isLaunchable() && $subject->getStatut() === 'ONLINE';
            case self::END:
                if (!$subject instanceof \App\Entity\Carpooling) {
                    return false;
                }
                return in_array('ROLE_DRIVER', $user->getRoles()) && $subject->getCreatedBy()->getId() === $user->getId() && $subject->getStatut() === 'IN_PROGRESS';
        }

        return false;
    }
}
