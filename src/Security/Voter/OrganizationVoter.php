<?php

namespace App\Security\Voter;

use App\Entity\Organization;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *
 */
class OrganizationVoter extends Voter
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
            && $subject instanceof Organization;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Organization $organization */
        $organization = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($organization, $user),
            self::EDIT => $this->canEdit($organization, $user),
            default => false
        };
    }

    /**
     * @param Organization $organization
     * @param UserInterface $user
     * @return bool
     */
    private function canView(Organization $organization, UserInterface $user): bool
    {
        // Admin peut tout voir
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Manager et Barman peuvent voir leur propre organisation
        if ($user instanceof User) {
            if (in_array('ROLE_MANAGER', $user->getRoles()) ||
                in_array('ROLE_BARMAN', $user->getRoles())) {
                return $user->getOrganization() === $organization;
            }
        }

        return false;
    }

    /**
     * @param Organization $organization
     * @param UserInterface $user
     * @return bool
     */
    private function canEdit(Organization $organization, UserInterface $user): bool
    {
        // Seul l'admin peut modifier
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Manager peut modifier sa propre organisation
        if ($user instanceof User && in_array('ROLE_MANAGER', $user->getRoles())) {
            return $user->getOrganization() === $organization;
        }

        // Le Barman ne peut pas modifier d'organisation
        return false;
    }
}