<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Car;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CarVoter extends Voter
{
    public const CREATE = 'CAR_CREATE';
    public const READ   = 'CAR_READ';
    public const UPDATE = 'CAR_UPDATE';
    public const DELETE = 'CAR_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::READ || self::CREATE) {
            return true;
        }

        return in_array($attribute, [self::UPDATE, self::DELETE])
            && $subject instanceof Car;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
            case self::READ:
                return in_array('ROLE_DRIVER', $user->getRoles());

            case self::UPDATE:
            case self::DELETE:
                if (!$subject instanceof \App\Entity\Car) {
                    return false;
                }
                return $subject->getUserId() === $user->getId();
        }

        return false;
    }
}
