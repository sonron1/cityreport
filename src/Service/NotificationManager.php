<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\Signalement;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;

class NotificationManager
{
  public function __construct(
      private EntityManagerInterface $entityManager
  ) {}

  /**
   * Crée une nouvelle notification
   */
  public function createNotification(
      Utilisateur $destinataire,
      string $type,
      string $message,
      ?Signalement $signalement = null
  ): Notification {
    $notification = new Notification();
    $notification->setDestinataire($destinataire);
    $notification->setType($type);
    $notification->setMessage($message);
    $notification->setSignalement($signalement);

    $this->entityManager->persist($notification);
    $this->entityManager->flush();

    return $notification;
  }

  /**
   * Récupère les notifications non lues d'un utilisateur
   */
  public function getUnreadNotifications(Utilisateur $utilisateur): array
  {
    return $this->entityManager->getRepository(Notification::class)
        ->findUnreadByUser($utilisateur);
  }

  /**
   * Compte les notifications non lues
   */
  public function countUnreadNotifications(Utilisateur $utilisateur): int
  {
    return $this->entityManager->getRepository(Notification::class)
        ->countUnreadByUser($utilisateur);
  }

  /**
   * Marque une notification comme lue
   */
  public function markAsRead(Notification $notification): void
  {
    $notification->marquerCommeLue();
    $this->entityManager->flush();
  }

  /**
   * Marque toutes les notifications comme lues pour un utilisateur
   */
  public function markAllAsRead(Utilisateur $utilisateur): void
  {
    $this->entityManager->getRepository(Notification::class)
        ->markAllAsReadForUser($utilisateur);
  }
}