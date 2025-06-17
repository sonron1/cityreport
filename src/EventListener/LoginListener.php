<?php

namespace App\EventListener;

use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

class LoginListener
{
    private $requestStack;
    private $urlGenerator;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $urlGenerator)
    {
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
    }

    public function onLoginSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        
        if (!$user instanceof Utilisateur) {
            return;
        }
        
        if (!$user->isEstValide()) {
            // Dans cette version de l'événement, nous ne pouvons pas directement modifier la réponse
            // Nous allons stocker une information en session pour que le contrôleur de sécurité redirige l'utilisateur
            $session = $this->requestStack->getSession();
            $session->set('_security_verification_needed', true);
            $session->getFlashBag()->add('warning', 'Votre compte n\'est pas encore vérifié. Veuillez vérifier votre email.');
        }
    }
}