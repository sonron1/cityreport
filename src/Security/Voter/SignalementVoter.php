<?php
// src/Security/Voter/SignalementVoter.php

namespace App\Security\Voter;

use App\Entity\Signalement;
use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SignalementVoter extends Voter
{
    const DELETE = 'delete';
    const EDIT = 'edit';
    const REQUEST_DELETE = 'request_delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::DELETE, self::EDIT, self::REQUEST_DELETE])
            && $subject instanceof Signalement;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Signalement $signalement */
        $signalement = $subject;

        /** @var Utilisateur $user */

        switch ($attribute) {
            case self::DELETE:
                return $this->canDelete($signalement, $user);
            case self::EDIT:
                return $this->canEdit($signalement, $user);
            case self::REQUEST_DELETE:
                return $this->canRequestDelete($signalement, $user);
        }

        return false;
    }

    private function canDelete(Signalement $signalement, UserInterface $user): bool
    {
        // Admins et modérateurs peuvent supprimer n'importe quel signalements
        return $this->security->isGranted('ROLE_MODERATOR');
    }

    private function canEdit(Signalement $signalement, UserInterface $user): bool
    {
        // L'auteur peut modifier son signalements si pas encore validé
        if ($signalement->getUtilisateur() === $user && $signalement->getEtatValidation() === 'en_attente') {
            return true;
        }

        // Admins et modérateurs peuvent modifier n'importe quel signalements
        return $this->security->isGranted('ROLE_MODERATOR');
    }

    private function canRequestDelete(Signalement $signalement, UserInterface $user): bool
    {
        // Seul l'auteur peut demander la suppression de son signalements
        return $signalement->getUtilisateur() === $user;
    }
}