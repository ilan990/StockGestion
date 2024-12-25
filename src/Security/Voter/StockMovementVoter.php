<?php

namespace App\Security\Voter;

use App\Entity\StockMovement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class StockMovementVoter extends Voter
{
    const VIEW = 'VIEW';
    const EDIT = 'EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof StockMovement;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var StockMovement $stockMovement */
        $stockMovement = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($stockMovement, $user),
            self::EDIT => $this->canEdit($stockMovement, $user),
            default => false
        };
    }

    private function canView(StockMovement $stockMovement, UserInterface $user): bool
    {
        // Admin peut tout voir
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Manager et Barman peuvent voir les mouvements de leur organisation
        if ($user instanceof User) {
            if (in_array('ROLE_MANAGER', $user->getRoles()) ||
                in_array('ROLE_BARMAN', $user->getRoles())) {
                return $user->getOrganization() === $stockMovement->getOrganization();
            }
        }

        return false;
    }

    private function canEdit(StockMovement $stockMovement, UserInterface $user): bool
    {
        // Admin peut tout modifier
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Manager peut modifier les mouvements de son organisation
        if ($user instanceof User && in_array('ROLE_MANAGER', $user->getRoles())) {
            return $user->getOrganization() === $stockMovement->getOrganization();
        }

        // Barman peut créer des mouvements dans son organisation
        if ($user instanceof User && in_array('ROLE_BARMAN', $user->getRoles())) {
            return $stockMovement->getUser() === $user &&
                $user->getOrganization() === $stockMovement->getOrganization();
        }

        return false;
    }
}