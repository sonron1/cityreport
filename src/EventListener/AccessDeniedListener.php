<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RequestStack;

class AccessDeniedListener implements EventSubscriberInterface
{
  public function __construct(
      private UrlGeneratorInterface $urlGenerator,
      private RequestStack $requestStack
  ) {}

  public static function getSubscribedEvents(): array
  {
    return [
        KernelEvents::EXCEPTION => 'onKernelException',
    ];
  }

  public function onKernelException(ExceptionEvent $event): void
  {
    $exception = $event->getThrowable();

    if (!$exception instanceof AccessDeniedException) {
      return;
    }

    $request = $event->getRequest();
    $session = $this->requestStack->getSession();

    // Détecter les différents cas d'accès refusé
    $route = $request->attributes->get('_route');
    $referer = $request->headers->get('referer');

    // Messages personnalisés selon le contexte
    $message = 'Accès refusé. Vous n\'avez pas les permissions nécessaires.';
    $redirectRoute = 'app_home';

    if (str_contains($route, 'delete') || str_contains($referer, 'signalements')) {
      $message = 'Seuls les administrateurs peuvent supprimer des signalements. En tant que modérateur, vous pouvez rejeter un signalements.';
      $redirectRoute = $request->headers->get('referer') ? null : 'app_signalements';
    } elseif (str_contains($route, 'admin')) {
      $message = 'Cette section est réservée aux administrateurs.';
      $redirectRoute = 'app_signalements';
    } elseif (str_contains($route, 'user') || str_contains($route, 'utilisateur')) {
      $message = 'Vous n\'avez pas les droits pour gérer les utilisateurs.';
      $redirectRoute = 'app_profil_show';
    }

    // Ajouter le message flash
    $session->getFlashBag()->add('error', $message);

    // Redirection
    if ($redirectRoute) {
      $redirectUrl = $this->urlGenerator->generate($redirectRoute);
    } else {
      // Retourner à la page précédente si disponible
      $redirectUrl = $request->headers->get('referer') ?: $this->urlGenerator->generate('app_home');
    }

    $response = new RedirectResponse($redirectUrl);
    $event->setResponse($response);
  }
}