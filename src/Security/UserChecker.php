<?php
namespace App\Security;

use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Utilisateur) {
            return;
        }

        if (!$user->isActif()) {
            throw new CustomUserMessageAccountStatusException(
                'Votre compte est désactivé. Contactez l’administrateur.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Not needed, unless you want extra checks after login
    }
}

?>