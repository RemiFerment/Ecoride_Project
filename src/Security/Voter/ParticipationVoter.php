<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ParticipationVoter extends Voter
{
    public const JOIN = 'PARTICIPATION_JOIN';
    public const LEAVE = 'PARTICIPATION_LEAVE';
    public const KICK = 'PARTICIPATION_KICK';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::JOIN, self::LEAVE, self::KICK])
            && $subject instanceof \App\Entity\Carpooling;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }


        switch ($attribute) {
            case self::JOIN:
                return $subject->getAvailableSeat() > 0 && $user !== $subject->getCreatedBy() && $subject->getStatut() === 'ONLINE' && !$subject->getParticipations()->exists(function ($key, $participation) use ($user) {
                    return $participation->getUser() === $user;
                }) && $user->getEcopiece() >= $subject->getPricePerPerson();
            case self::LEAVE:
                return $subject->getParticipations()->exists(function ($key, $participation) use ($user) {
                    return $participation->getUser() === $user;
                }) && $subject->getStatut() === 'ONLINE' && $subject->getStartDate() > new \DateTimeImmutable('+2 hour');
            case self::KICK:
                return $user === $subject->getCreatedBy() && $subject->getStatut() === 'ONLINE' && $subject->getStartDate() > new \DateTimeImmutable('+2 hour');
        }

        return false;
    }
}
