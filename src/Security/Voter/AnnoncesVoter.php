<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AnnoncesVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        
        return in_array($attribute, ['EDIT', 'DELETE'])
            && $subject instanceof \App\Entity\Annonces;
    }

    protected function voteOnAttribute($attribute, $annonce, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'EDIT':
                return $annonce->getUsers()->getId() == $user->getId();
                break;
            case 'DELETE':
                return $annonce->getUsers()->getId() == $user->getId();
                break;
        }

        return false;
    }
}
