<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *
 */
class CategoryVoter extends Voter
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
            && $subject instanceof Category;
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

        /** @var Category $category */
        $category = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($category, $user),
            self::EDIT => $this->canEdit($category, $user),
            default => false
        };
    }

    /**
     * @param Category $category
     * @param UserInterface $user
     * @return bool
     */
    private function canView(Category $category, UserInterface $user): bool
    {
        // Admin peut tout voir
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Manager et Barman peuvent voir les catégories de leur organisation
        if ($user instanceof User) {
            if (in_array('ROLE_MANAGER', $user->getRoles()) ||
                in_array('ROLE_BARMAN', $user->getRoles())) {
                return $user->getOrganization() === $category->getOrganization();
            }
        }

        return false;
    }

    /**
     * @param Category $category
     * @param UserInterface $user
     * @return bool
     */
    private function canEdit(Category $category, UserInterface $user): bool
    {
        // Admin peut tout modifier
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Manager peut modifier les catégories de son organisation
        if ($user instanceof User && in_array('ROLE_MANAGER', $user->getRoles())) {
            return $user->getOrganization() === $category->getOrganization();
        }

        return false;
    }
}