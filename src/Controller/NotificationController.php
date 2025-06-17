<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notifications')]
class NotificationController extends AbstractController
{
  public function __construct(
      private NotificationService $notificationService,
      private EntityManagerInterface $entityManager
  ) {}

  /**
   * ✅ PAGE PRINCIPALE DES NOTIFICATIONS
   */
  #[Route('/', name: 'app_notifications_index')]
  public function index(): Response
  {
    $user = $this->getUser();
    if (!$user) {
      return $this->redirectToRoute('app_login');
    }

    $notifications = $this->notificationService->getToutesNotifications($user, 50);

    return $this->render('notification/index.html.twig', [
        'notifications' => $notifications,
        'nombreNonLues' => $this->notificationService->compterNotificationsNonLues($user)
    ]);
  }

  /**
   * ✅ API POUR RÉCUPÉRER LES NOTIFICATIONS (AJAX)
   */
  #[Route('/api/liste', name: 'app_notifications_api', methods: ['GET'])]
  public function apiListe(): JsonResponse
  {
    $user = $this->getUser();
    if (!$user) {
      return new JsonResponse(['error' => 'Non connecté'], 401);
    }

    $notifications = $this->notificationService->getNotificationsNonLues($user);
    $nombreNonLues = count($notifications);

    $notificationsData = [];
    foreach ($notifications as $notification) {
      $notificationsData[] = [
          'id' => $notification->getId(),
          'message' => $notification->getMessage(),
          'type' => $notification->getType(),
          'typeIcon' => $notification->getTypeIcon(),
          'typeClass' => $notification->getTypeClass(),
          'dateEnvoi' => $notification->getDateEnvoi()->format('d/m/Y H:i'),
          'dateRelative' => $this->getDateRelative($notification->getDateEnvoi()),
          'signalementId' => $notification->getSignalement()?->getId(),
          'isLue' => $notification->isLue()
      ];
    }

    return new JsonResponse([
        'notifications' => $notificationsData,
        'nombreNonLues' => $nombreNonLues
    ]);
  }

  /**
   * ✅ MARQUER UNE NOTIFICATION COMME LUE
   */
  #[Route('/{id}/lire', name: 'app_notification_marquer_lue', methods: ['POST'])]
  public function marquerCommeLue(Notification $notification): JsonResponse
  {
    if ($notification->getDestinataire() !== $this->getUser()) {
      return new JsonResponse(['error' => 'Non autorisé'], 403);
    }

    $this->notificationService->marquerCommeLue($notification);

    return new JsonResponse(['success' => true]);
  }

  /**
   * ✅ MARQUER TOUTES LES NOTIFICATIONS COMME LUES
   */
  #[Route('/marquer-toutes-lues', name: 'app_notifications_marquer_toutes_lues', methods: ['POST'])]
  public function marquerToutesCommeLues(): JsonResponse
  {
    $user = $this->getUser();
    if (!$user) {
      return new JsonResponse(['error' => 'Non connecté'], 401);
    }

    $this->notificationService->marquerToutesCommeLues($user);

    return new JsonResponse(['success' => true]);
  }

  /**
   * ✅ SUPPRIMER UNE NOTIFICATION
   */
  #[Route('/{id}/supprimer', name: 'app_notification_supprimer', methods: ['DELETE', 'POST'])]
  public function supprimer(Notification $notification): JsonResponse
  {
    if ($notification->getDestinataire() !== $this->getUser()) {
      return new JsonResponse(['error' => 'Non autorisé'], 403);
    }

    $this->entityManager->remove($notification);
    $this->entityManager->flush();

    return new JsonResponse(['success' => true]);
  }

  /**
   * ✅ HELPER : DATE RELATIVE
   */
  private function getDateRelative(\DateTime $date): string
  {
    $now = new \DateTime();
    $diff = $now->diff($date);

    if ($diff->days > 7) {
      return $date->format('d/m/Y');
    } elseif ($diff->days > 0) {
      return $diff->days . ' jour' . ($diff->days > 1 ? 's' : '');
    } elseif ($diff->h > 0) {
      return $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
    } elseif ($diff->i > 0) {
      return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    } else {
      return 'À l\'instant';
    }
  }
}