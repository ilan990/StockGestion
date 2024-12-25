<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *
 */
class UserVoter extends Voter
{
    /**
     *
     */
    const VIEW = 'VIEW';
    /**
     *
     */
    const EDIT = 'EDIT';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof User;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();
        if (!$currentUser instanceof UserInterface) {
            return false;
        }

        /** @var User $user */
        $user = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($user, $currentUser),
            self::EDIT => $this->canEdit($user, $currentUser),
            default => false
        };
    }

    /**
     * @param User $user
     * @param UserInterface $currentUser
     * @return bool
     */
    private function canView(User $user, UserInterface $currentUser): bool
    {
        // Admin peut tout voir
        if (in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return true;
        }

        // Manager peut voir dans son organisation
        if (in_array('ROLE_MANAGER', $currentUser->getRoles())) {
            return $user->getOrganization() === $currentUser->getOrganization();
        }

        // Un user peut se voir lui-même
        return $currentUser === $user;
    }

    /**
     * @param User $user
     * @param UserInterface $currentUser
     * @return bool
     */
    private function canEdit(User $user, UserInterface $currentUser): bool
    {
        // Logique similaire pour l'édition
        if (in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return true;
        }

        // Manager peut éditer dans son organisation sauf les admins
        if (in_array('ROLE_MANAGER', $currentUser->getRoles())) {
            return $user->getOrganization() === $currentUser->getOrganization()
                && !in_array('ROLE_ADMIN', $user->getRoles());
        }

        return false;
    }
}