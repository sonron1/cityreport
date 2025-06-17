<?php

namespace App\Service;

use App\Entity\Signalement;
use App\Entity\Utilisateur;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EmailService
{
  public function __construct(
      private MailerInterface $mailer,
      private Environment $twig,
      private UrlGeneratorInterface $urlGenerator,
      private string $emailSender
  ) {}

  public function sendSignalementValidatedEmail(Signalement $signalement): void
  {
    $utilisateur = $signalement->getUtilisateur();

    if (!$utilisateur || !$utilisateur->getEmail()) {
      return;
    }

    $email = (new Email())
        ->from($this->emailSender)
        ->to($utilisateur->getEmail())
        ->subject('✅ Votre signalement a été validé')
        ->html($this->twig->render('emails/signalement_valide.html.twig', [
            'signalement' => $signalement,
            'utilisateur' => $utilisateur,
            'signalementUrl' => $this->generateSignalementUrl($signalement)
        ]));

    $this->mailer->send($email);
  }

  public function sendSignalementRejectedEmail(Signalement $signalement, string $commentaire = ''): void
  {
    $utilisateur = $signalement->getUtilisateur();

    if (!$utilisateur || !$utilisateur->getEmail()) {
      return;
    }

    $email = (new Email())
        ->from($this->emailSender)
        ->to($utilisateur->getEmail())
        ->subject('❌ Votre signalement a été rejeté')
        ->html($this->twig->render('emails/signalement_rejete.html.twig', [
            'signalement' => $signalement,
            'utilisateur' => $utilisateur,
            'commentaire' => $commentaire,
            'modifierUrl' => $this->generateSignalementModifierUrl($signalement)
        ]));

    $this->mailer->send($email);
  }

  public function sendSignalementStatusUpdateEmail(Signalement $signalement, string $ancienStatut, string $nouveauStatut): void
  {
    $utilisateur = $signalement->getUtilisateur();

    if (!$utilisateur || !$utilisateur->getEmail()) {
      return;
    }

    $email = (new Email())
        ->from($this->emailSender)
        ->to($utilisateur->getEmail())
        ->subject('🔄 Mise à jour de votre signalement')
        ->html($this->twig->render('emails/signalement_statut_update.html.twig', [
            'signalement' => $signalement,
            'utilisateur' => $utilisateur,
            'ancienStatut' => $ancienStatut,
            'nouveauStatut' => $nouveauStatut,
            'signalementUrl' => $this->generateSignalementUrl($signalement)
        ]));

    $this->mailer->send($email);
  }

  public function sendSignalementResolvedEmail(Signalement $signalement): void
  {
    $utilisateur = $signalement->getUtilisateur();

    if (!$utilisateur || !$utilisateur->getEmail()) {
      return;
    }

    $email = (new Email())
        ->from($this->emailSender)
        ->to($utilisateur->getEmail())
        ->subject('🎉 Votre signalement a été résolu')
        ->html($this->twig->render('emails/signalement_resolu.html.twig', [
            'signalement' => $signalement,
            'utilisateur' => $utilisateur,
            'signalementUrl' => $this->generateSignalementUrl($signalement)
        ]));

    $this->mailer->send($email);
  }

  public function sendSignalementCommentEmail(Signalement $signalement, string $commentaire, string $auteur): void
  {
    $utilisateur = $signalement->getUtilisateur();

    if (!$utilisateur || !$utilisateur->getEmail()) {
      return;
    }

    $email = (new Email())
        ->from($this->emailSender)
        ->to($utilisateur->getEmail())
        ->subject('💬 Nouveau commentaire sur votre signalement')
        ->html($this->twig->render('emails/signalement_commentaire.html.twig', [
            'signalement' => $signalement,
            'utilisateur' => $utilisateur,
            'commentaire' => $commentaire,
            'auteur' => $auteur,
            'signalementUrl' => $this->generateSignalementUrl($signalement)
        ]));

    $this->mailer->send($email);
  }

  public function sendWelcomeEmail(Utilisateur $utilisateur): void
  {
    if (!$utilisateur->getEmail()) {
      return;
    }

    $email = (new Email())
        ->from($this->emailSender)
        ->to($utilisateur->getEmail())
        ->subject('🎉 Bienvenue sur CityFlow Bénin !')
        ->html($this->twig->render('emails/bienvenue.html.twig', [
            'utilisateur' => $utilisateur
        ]));

    $this->mailer->send($email);
  }

  public function sendAccountValidatedEmail(Utilisateur $utilisateur): void
  {
    if (!$utilisateur->getEmail()) {
      return;
    }

    $email = (new Email())
        ->from($this->emailSender)
        ->to($utilisateur->getEmail())
        ->subject('✅ Votre compte a été validé')
        ->html($this->twig->render('emails/compte_valide.html.twig', [
            'utilisateur' => $utilisateur
        ]));

    $this->mailer->send($email);
  }

  public function sendPasswordResetEmail(Utilisateur $utilisateur, string $token): void
  {
    if (!$utilisateur->getEmail()) {
      return;
    }

    // Utiliser une route existante ou créer une URL simple
    $resetUrl = $this->generateSafeUrl('app_login') . '?reset_token=' . $token;

    $email = (new Email())
        ->from($this->emailSender)
        ->to($utilisateur->getEmail())
        ->subject('🔑 Réinitialisation de votre mot de passe')
        ->html($this->twig->render('emails/reset_mot_de_passe.html.twig', [
            'utilisateur' => $utilisateur,
            'resetUrl' => $resetUrl,
            'expirationHours' => 24
        ]));

    $this->mailer->send($email);
  }

  public function sendConfirmationEmail(Utilisateur $utilisateur, string $token): void
  {
    if (!$utilisateur->getEmail()) {
      return;
    }

    // Utiliser une route existante
    $confirmationUrl = $this->generateSafeUrl('app_request_verify_email') . '?token=' . $token;

    $email = (new Email())
        ->from($this->emailSender)
        ->to($utilisateur->getEmail())
        ->subject('🔐 Confirmez votre adresse email - CityFlow Bénin')
        ->html($this->twig->render('emails/confirmation_email.html.twig', [
            'utilisateur' => $utilisateur,
            'confirmationUrl' => $confirmationUrl
        ]));

    $this->mailer->send($email);
  }

  public function sendSignalementDeletedEmail(Utilisateur $utilisateur, string $titreSignalement, string $raison = ''): void
  {
    if (!$utilisateur->getEmail()) {
      return;
    }

    // Utiliser la route admin dashboard qui existe
    $dashboardUrl = $this->generateSafeUrl('app_admin_dashboard');

    $email = (new Email())
        ->from($this->emailSender)
        ->to($utilisateur->getEmail())
        ->subject('🗑️ Signalement supprimé')
        ->html($this->twig->render('emails/signalement_supprime.html.twig', [
            'utilisateur' => $utilisateur,
            'titreSignalement' => $titreSignalement,
            'raison' => $raison,
            'dashboardUrl' => $dashboardUrl
        ]));

    $this->mailer->send($email);
  }

  /**
   * Génère l'URL du signalement de manière sécurisée
   */
  private function generateSignalementUrl(Signalement $signalement): string
  {
    if (!$signalement->getId()) {
      return '#';
    }

    try {
      return $this->urlGenerator->generate(
          'app_signalement_show',
          ['id' => $signalement->getId()],
          UrlGeneratorInterface::ABSOLUTE_URL
      );
    } catch (\Exception $e) {
      return '#';
    }
  }

  /**
   * Génère l'URL de modification du signalement de manière sécurisée
   */
  private function generateSignalementModifierUrl(Signalement $signalement): string
  {
    if (!$signalement->getId()) {
      return '#';
    }

    try {
      return $this->urlGenerator->generate(
          'app_signalement_modifier',
          ['id' => $signalement->getId()],
          UrlGeneratorInterface::ABSOLUTE_URL
      );
    } catch (\Exception $e) {
      return '#';
    }
  }

  /**
   * Génère une URL de manière sécurisée
   */
  private function generateSafeUrl(string $routeName): string
  {
    try {
      return $this->urlGenerator->generate(
          $routeName,
          [],
          UrlGeneratorInterface::ABSOLUTE_URL
      );
    } catch (\Exception $e) {
      return 'http://localhost:8000'; // URL de fallback
    }
  }
}