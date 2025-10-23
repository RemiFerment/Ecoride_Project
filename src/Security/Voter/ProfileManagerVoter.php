<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ProfileManagerVoter extends Voter
{
    public const READ = 'PROFILE_READ';
    public const EDIT = 'PROFILE_EDIT';
    public const DELETE = 'PROFILE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::READ) {
            return true;
        }
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                return $user === $subject;
            case self::READ:
                return true;
        }

        return false;
    }
}
