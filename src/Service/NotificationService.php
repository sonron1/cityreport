<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\Utilisateur;
use App\Entity\Signalement;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class NotificationService
{
  public function __construct(
      private EntityManagerInterface $entityManager,
      private EmailService $emailService,  // ✅ PARAMÈTRE CORRECTEMENT NOMMÉ
      private LoggerInterface $logger
  ) {}

  /**
   * ✅ CRÉER UNE NOTIFICATION
   */
  public function creerNotification(
      Utilisateur $destinataire,
      string $message,
      string $type,
      ?Signalement $signalement = null
  ): Notification {
    $notification = new Notification();
    $notification->setDestinataire($destinataire);
    $notification->setMessage($message);
    $notification->setType($type);
    $notification->setSignalement($signalement);
    $notification->setStatut('non_lue');
    $notification->setDateEnvoi(new \DateTime());

    $this->entityManager->persist($notification);
    $this->entityManager->flush();

    return $notification;
  }

  /**
   * ✅ NOTIFIER LA VALIDATION D'UN SIGNALEMENT
   */
  public function notifierValidation(Signalement $signalement, ?string $commentaire = null): void
  {
    $message = "✅ Votre signalement \"" . $signalement->getTitre() . "\" a été validé par l'équipe de modération.";

    if ($commentaire) {
      $message .= " Commentaire : " . $commentaire;
    }

    // Créer la notification
    $this->creerNotification(
        $signalement->getUtilisateur(),
        $message,
        'validation',
        $signalement
    );

    // ✅ ENVOYER L'EMAIL AVEC LOGGING PROFESSIONNEL
    try {
      $this->emailService->sendSignalementValidatedEmail($signalement);
    } catch (\Exception $e) {
      $this->logger->warning('Échec envoi email validation', [
          'signalement_id' => $signalement->getId(),
          'user_id' => $signalement->getUtilisateur()->getId(),
          'exception' => $e->getMessage()
      ]);
    }
  }

  /**
   * ✅ NOTIFIER LE REJET D'UN SIGNALEMENT
   */
  public function notifierRejet(Signalement $signalement, ?string $motif = null): void
  {
    $message = "❌ Votre signalement \"" . $signalement->getTitre() . "\" a été rejeté par l'équipe de modération.";

    if ($motif) {
      $message .= " Motif : " . $motif;
    }

    // Créer la notification
    $this->creerNotification(
        $signalement->getUtilisateur(),
        $message,
        'rejet',
        $signalement
    );

    // ✅ ENVOYER L'EMAIL
    try {
      $this->emailService->sendSignalementRejectedEmail($signalement, $motif ?? '');
    } catch (\Exception $e) {
      $this->logger->warning('Échec envoi email rejet', [
          'signalement_id' => $signalement->getId(),
          'user_id' => $signalement->getUtilisateur()->getId(),
          'exception' => $e->getMessage()
      ]);
    }
  }

  /**
   * ✅ NOTIFIER LE CHANGEMENT DE STATUT
   */
  public function notifierChangementStatut(Signalement $signalement, string $ancienStatut, string $nouveauStatut): void
  {
    $statutsLabels = [
        'nouveau' => 'Nouveau',
        'en_cours' => 'En cours de traitement',
        'resolu' => 'Résolu',
        'annule' => 'Annulé'
    ];

    $message = "🔄 Le statut de votre signalement \"" . $signalement->getTitre() . "\" est passé de \""
        . ($statutsLabels[$ancienStatut] ?? $ancienStatut) . "\" à \""
        . ($statutsLabels[$nouveauStatut] ?? $nouveauStatut) . "\".";

    // Créer la notification
    $this->creerNotification(
        $signalement->getUtilisateur(),
        $message,
        'statut_change',
        $signalement
    );

    // ✅ ENVOYER L'EMAIL SELON LE STATUT
    try {
      if ($nouveauStatut === 'resolu') {
        $this->emailService->sendSignalementResolvedEmail($signalement);
      } else {
        $this->emailService->sendSignalementStatusUpdateEmail($signalement, $ancienStatut, $nouveauStatut);
      }
    } catch (\Exception $e) {
      $this->logger->warning('Échec envoi email changement statut', [
          'signalement_id' => $signalement->getId(),
          'user_id' => $signalement->getUtilisateur()->getId(),
          'exception' => $e->getMessage()
      ]);
    }
  }

  /**
   * ✅ NOTIFIER UN NOUVEAU COMMENTAIRE
   */
  public function notifierNouveauCommentaire(Signalement $signalement, Utilisateur $auteurCommentaire): void
  {
    // Ne pas notifier l'auteur du signalement s'il commente son propre signalement
    if ($signalement->getUtilisateur() === $auteurCommentaire) {
      return;
    }

    $message = "💬 Un nouveau commentaire a été ajouté sur votre signalement \"" . $signalement->getTitre() . "\" par "
        . $auteurCommentaire->getPrenom() . " " . $auteurCommentaire->getNom() . ".";

    // Créer la notification
    $this->creerNotification(
        $signalement->getUtilisateur(),
        $message,
        'commentaire',
        $signalement
    );

    // ✅ ENVOYER L'EMAIL
    try {
      $commentaireTexte = $message; // Ou récupérer le vrai commentaire si disponible
      $auteurNom = $auteurCommentaire->getPrenom() . " " . $auteurCommentaire->getNom();
      $this->emailService->sendSignalementCommentEmail($signalement, $commentaireTexte, $auteurNom);
    } catch (\Exception $e) {
      $this->logger->warning('Échec envoi email commentaire', [
          'signalement_id' => $signalement->getId(),
          'user_id' => $auteurCommentaire->getId(),
          'exception' => $e->getMessage()
      ]);
    }
  }

  /**
   * ✅ RÉCUPÉRER LES NOTIFICATIONS NON LUES
   */
  public function getNotificationsNonLues(Utilisateur $utilisateur): array
  {
    return $this->entityManager->getRepository(Notification::class)
        ->findBy([
            'destinataire' => $utilisateur,
            'statut' => 'non_lue'
        ], ['dateEnvoi' => 'DESC']);
  }

  /**
   * ✅ COMPTER LES NOTIFICATIONS NON LUES
   */
  public function compterNotificationsNonLues(Utilisateur $utilisateur): int
  {
    return $this->entityManager->getRepository(Notification::class)
        ->count([
            'destinataire' => $utilisateur,
            'statut' => 'non_lue'
        ]);
  }

  /**
   * ✅ MARQUER UNE NOTIFICATION COMME LUE
   */
  public function marquerCommeLue(Notification $notification): void
  {
    $notification->setStatut('lue');
    $this->entityManager->flush();
  }

  /**
   * ✅ MARQUER TOUTES LES NOTIFICATIONS COMME LUES
   */
  public function marquerToutesCommeLues(Utilisateur $utilisateur): void
  {
    $notifications = $this->entityManager->getRepository(Notification::class)
        ->findBy([
            'destinataire' => $utilisateur,
            'statut' => 'non_lue'
        ]);

    foreach ($notifications as $notification) {
      $notification->setStatut('lue');
    }

    $this->entityManager->flush();
  }

  /**
   * ✅ RÉCUPÉRER TOUTES LES NOTIFICATIONS (AVEC PAGINATION)
   */
  public function getToutesNotifications(Utilisateur $utilisateur, int $limite = 20): array
  {
    return $this->entityManager->getRepository(Notification::class)
        ->findBy([
            'destinataire' => $utilisateur
        ], ['dateEnvoi' => 'DESC'], $limite);
  }

  /**
   * ✅ SUPPRIMER LES ANCIENNES NOTIFICATIONS (nettoyage)
   */
  public function supprimerAnciennesNotifications(int $joursConservation = 30): int
  {
    $dateLimit = new \DateTime("-{$joursConservation} days");

    $query = $this->entityManager->createQuery(
        'DELETE FROM App\Entity\Notification n WHERE n.dateEnvoi < :dateLimit'
    );
    $query->setParameter('dateLimit', $dateLimit);

    return $query->execute();
  }
}