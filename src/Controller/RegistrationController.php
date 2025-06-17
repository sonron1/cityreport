<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormTypeForm as RegistrationFormType;
use App\Service\TokenService;
use App\Service\RegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RegistrationController extends AbstractController
{
  public function __construct(
      private LoggerInterface $logger,
      private TokenService $tokenService,
      private RegistrationService $registrationService
  ) {}

  #[Route('/register', name: 'app_register')]
  public function register(
      Request $request,
      UserPasswordHasherInterface $userPasswordHasher,
      EntityManagerInterface $entityManager,
      MailerInterface $mailer,
      RateLimiterFactory $registrationLimiter = null
  ): Response {
    // Protection contre le spam d'inscription
    if ($registrationLimiter) {
      $limiter = $registrationLimiter->create($request->getClientIp());
      if (!$limiter->consume()->isAccepted()) {
        $this->addFlash('error', 'Trop de tentatives d\'inscription. Veuillez réessayer plus tard.');
        return $this->redirectToRoute('app_register');
      }
    }

    $user = new Utilisateur();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $connection = $entityManager->getConnection();
      $connection->beginTransaction();

      try {
        // Vérifier si l'email existe déjà dans une transaction
        $existingUser = $entityManager->getRepository(Utilisateur::class)
            ->findOneBy(['email' => $user->getEmail()]);

        if ($existingUser) {
          $connection->rollBack();

          if ($existingUser->isEstValide()) {
            $this->addFlash('error', 'Un compte avec cette adresse email existe déjà et est actif.');
            $this->addFlash('info', 'Connectez-vous si c\'est votre compte.');
          } else {
            $this->addFlash('warning', 'Un compte avec cette adresse email existe déjà mais n\'est pas encore validé.');
            $this->addFlash('info', 'Vérifiez vos emails ou demandez un nouveau lien de validation.');
          }
          return $this->redirectToRoute('app_register');
        }

        // Encoder le mot de passe
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        // Génération sécurisée du token de confirmation
        $token = $this->tokenService->generateSecureToken();
        $user->setConfirmationToken($token);

        // Définition de la date d'expiration (24 heures)
        $expiry = new \DateTime();
        $expiry->modify('+24 hours');
        $user->setTokenExpiryDate($expiry);

        // Le compte n'est pas validé par défaut
        $user->setEstValide(false);

        $entityManager->persist($user);
        $entityManager->flush();

        // Envoi de l'email de confirmation
        $this->sendConfirmationEmail($user, $token, $expiry, $mailer);

        $connection->commit();

        $this->logger->info('Nouvel utilisateur inscrit', [
            'user_id' => $user->getId(),
            'email' => $user->getEmail()
        ]);

        // Redirection sécurisée vers la page d'attente
        return $this->redirectToRoute('app_registration_waiting', [
            'email' => $user->getEmail()
        ]);

      } catch (\Exception $e) {
        $connection->rollBack();

        $this->logger->error('Erreur lors de l\'inscription', [
            'email' => $user->getEmail(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        $this->addFlash('error', 'Une erreur est survenue lors de la création de votre compte. Veuillez réessayer.');
      }
    }

    return $this->render('registration/register.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
  }

  #[Route('/register/waiting', name: 'app_registration_waiting')]
  public function waiting(Request $request): Response
  {
    $email = $request->query->get('email');

    // Validation sécurisée sans révélation d'informations
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $this->addFlash('error', 'Accès invalide.');
      return $this->redirectToRoute('app_register');
    }

    return $this->render('registration/waiting.html.twig', [
        'email' => $email,
    ]);
  }

  #[Route('/verify/email/{token}', name: 'app_verify_email')]
  public function verifyUserEmail(
      string $token,
      EntityManagerInterface $entityManager,
      RateLimiterFactory $emailVerificationLimiter = null
  ): Response {
    // Protection contre les attaques par force brute
    if ($emailVerificationLimiter) {
      $limiter = $emailVerificationLimiter->create('email_verification');
      if (!$limiter->consume()->isAccepted()) {
        $this->addFlash('error', 'Trop de tentatives de vérification. Veuillez réessayer plus tard.');
        return $this->redirectToRoute('app_login');
      }
    }

    // Validation du format du token
    if (!$this->tokenService->isValidTokenFormat($token)) {
      $this->addFlash('error', 'Le lien de vérification est invalide.');
      return $this->redirectToRoute('app_login');
    }

    $userRepository = $entityManager->getRepository(Utilisateur::class);
    $user = $userRepository->findOneBy(['confirmationToken' => $token]);

    if (!$user) {
      $this->logger->warning('Tentative de vérification avec token invalide', ['token' => substr($token, 0, 10) . '...']);

      $this->addFlash('error', 'Le lien de vérification est invalide ou a déjà été utilisé.');
      $this->addFlash('info', 'Vous pouvez demander un nouveau lien de vérification.');
      return $this->redirectToRoute('app_login');
    }

    if ($user->isTokenExpired()) {
      $this->addFlash('error', 'Le lien de vérification a expiré.');
      $this->addFlash('info', 'Demandez un nouveau lien de vérification.');
      return $this->redirectToRoute('app_request_verify_email');
    }

    // Validation du compte
    $user->setEstValide(true);
    $user->setConfirmationToken(null);
    $user->setTokenExpiryDate(null);

    $entityManager->flush();

    $this->logger->info('Email vérifié avec succès', [
        'user_id' => $user->getId(),
        'email' => $user->getEmail()
    ]);

    $this->addFlash('success', 'Félicitations ! Votre adresse email a été vérifiée avec succès.');
    $this->addFlash('info', 'Vous pouvez maintenant vous connecter et commencer à signaler des problèmes urbains.');

    return $this->redirectToRoute('app_login');
  }

  #[Route('/verify/resend', name: 'app_request_verify_email')]
  public function requestVerifyEmail(): Response
  {
    return $this->render('registration/request_verify_email.html.twig');
  }

  #[Route('/verify/resend/send', name: 'app_resend_verify_email', methods: ['POST'])]
  public function resendVerifyEmail(
      Request $request,
      EntityManagerInterface $entityManager,
      MailerInterface $mailer,
      RateLimiterFactory $resendEmailLimiter = null
  ): Response {
    // Protection contre le spam de demandes de renvoi
    if ($resendEmailLimiter) {
      $limiter = $resendEmailLimiter->create($request->getClientIp());
      if (!$limiter->consume()->isAccepted()) {
        $this->addFlash('error', 'Trop de demandes de renvoi. Veuillez patienter avant de réessayer.');
        return $this->redirectToRoute('app_request_verify_email');
      }
    }

    $emailAddress = $request->request->get('email');

    if (!$emailAddress || !filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
      $this->addFlash('error', 'Veuillez fournir une adresse email valide.');
      return $this->redirectToRoute('app_request_verify_email');
    }

    $userRepository = $entityManager->getRepository(Utilisateur::class);
    $user = $userRepository->findOneBy(['email' => $emailAddress]);

    // Réponse identique pour tous les cas (sécurité)
    $successMessage = 'Si votre adresse email existe dans notre système et n\'est pas encore vérifiée, un nouvel email de vérification vous a été envoyé.';

    if (!$user) {
      $this->addFlash('success', $successMessage);
      return $this->redirectToRoute('app_login');
    }

    if ($user->isEstValide()) {
      $this->addFlash('success', $successMessage);
      return $this->redirectToRoute('app_login');
    }

    try {
      // Génération d'un nouveau token
      $token = $this->tokenService->generateSecureToken();
      $user->setConfirmationToken($token);

      // Définition de la date d'expiration (24 heures)
      $expiry = new \DateTime();
      $expiry->modify('+24 hours');
      $user->setTokenExpiryDate($expiry);

      $entityManager->flush();

      // Envoi de l'email de confirmation
      $this->sendConfirmationEmail($user, $token, $expiry, $mailer, true);

      $this->logger->info('Email de vérification renvoyé', [
          'user_id' => $user->getId(),
          'email' => $user->getEmail()
      ]);

      $this->addFlash('success', $successMessage);

    } catch (\Exception $e) {
      $this->logger->error('Erreur lors du renvoi de l\'email de vérification', [
          'email' => $emailAddress,
          'error' => $e->getMessage()
      ]);

      $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer dans quelques minutes.');
    }

    return $this->redirectToRoute('app_login');
  }

  private function sendConfirmationEmail(
      Utilisateur $user,
      string $token,
      \DateTime $expiry,
      MailerInterface $mailer,
      bool $isResend = false
  ): void {
    $email = (new TemplatedEmail())
        ->from(new Address($this->getParameter('app.email_sender'), 'CityFlow Bénin'))
        ->to($user->getEmail())
        ->subject($isResend ? 'Nouveau lien de confirmation - CityFlow Bénin' : 'Confirmation de votre compte CityFlow Bénin')
        ->htmlTemplate('emails/confirmation.html.twig')
        ->context([
            'user' => $user,
            'token' => $token,
            'expiry' => $expiry,
            'isResend' => $isResend,
        ]);

    $mailer->send($email);
  }
}