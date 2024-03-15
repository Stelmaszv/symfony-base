<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BaseUserVoter extends Voter
{
    public const USER = 'ROLE_USER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Check if the attribute is relevant to this voter
        return \in_array($attribute, ['VIEW', 'EDIT', 'DELETE'], true); // List supported attributes

        // Additionally, ensure the subject is a BaseUser entity
        // (consider adding an interface for generic user handling)
        // and replace with your actual user entity class if needed
        // return $subject instanceof \App\Entity\BaseUser;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Deny access if user is not authenticated
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Grant access to admins for all attributes
        if ($user->hasRole(self::USER)) { // Use constant for clarity
            return VoterInterface::ACCESS_GRANTED;
        }

        // Implement additional logic for specific attributes here
        // (e.g., check ownership for editing/deleting)

        // If no specific logic applies, abstain
        return VoterInterface::ACCESS_ABSTAIN;
    }
}
