<?php

namespace App\Security\Voter;

use App\Entity\Bottle;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BottleVoter extends Voter
{
    const VIEW = 'VIEW';
    const EDIT = 'EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Bottle;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Bottle $bottle */
        $bottle = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($bottle, $user),
            self::EDIT => $this->canEdit($bottle, $user),
            default => false
        };
    }

    private function canView(Bottle $bottle, UserInterface $user): bool
    {
        // Admin peut tout voir
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Manager et Barman peuvent voir les bouteilles de leur organisation
        if ($user instanceof User) {
            if (in_array('ROLE_MANAGER', $user->getRoles()) ||
                in_array('ROLE_BARMAN', $user->getRoles())) {
                return $user->getOrganization() === $bottle->getOrganization();
            }
        }

        return false;
    }

    private function canEdit(Bottle $bottle, UserInterface $user): bool
    {
        // Admin peut tout modifier
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Manager peut modifier les bouteilles de son organisation
        if ($user instanceof User && in_array('ROLE_MANAGER', $user->getRoles())) {
            return $user->getOrganization() === $bottle->getOrganization();
        }

        return false;
    }
}