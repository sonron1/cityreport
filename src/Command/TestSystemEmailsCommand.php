<?php

namespace App\Command;

use App\Service\EmailService;
use App\Entity\Utilisateur;
use App\Entity\Signalement;
use App\Repository\UtilisateurRepository;
use App\Repository\SignalementRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-system-emails',
    description: 'Test tous les types d\'emails du système CityFlow avec Brevo',
)]
class TestSystemEmailsCommand extends Command
{
  public function __construct(
      private EmailService $emailService,
      private UtilisateurRepository $utilisateurRepository,
      private SignalementRepository $signalementRepository
  ) {
    parent::__construct();
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    $io->title('🧪 Test complet des emails CityFlow avec Brevo');

    // Créer un utilisateur test
    $utilisateurTest = $this->createTestUser();

    // Tests des emails
    $tests = [
        'welcome' => '🎉 Email de bienvenue',
        'account_validated' => '✅ Compte validé',
        'password_reset' => '🔑 Réinitialisation mot de passe',
        'confirmation' => '🔐 Confirmation email',
        'signalement_deleted' => '🗑️ Signalement supprimé'
    ];

    $successCount = 0;

    foreach ($tests as $testKey => $testName) {
      $io->section($testName);

      try {
        switch ($testKey) {
          case 'welcome':
            $this->emailService->sendWelcomeEmail($utilisateurTest);
            break;

          case 'account_validated':
            $this->emailService->sendAccountValidatedEmail($utilisateurTest);
            break;

          case 'password_reset':
            $this->emailService->sendPasswordResetEmail($utilisateurTest, 'test-token-123');
            break;

          case 'confirmation':
            $this->emailService->sendConfirmationEmail($utilisateurTest, 'test-confirmation-456');
            break;

          case 'signalement_deleted':
            $this->emailService->sendSignalementDeletedEmail(
                $utilisateurTest,
                'Test Signalement',
                'Test de suppression'
            );
            break;
        }

        $io->success("✅ {$testName} envoyé avec succès !");
        $successCount++;

      } catch (\Exception $e) {
        $io->error("❌ Échec {$testName} : " . $e->getMessage());
      }
    }

    // Test avec signalement si disponible
    $signalement = $this->signalementRepository->findOneBy([]);
    if ($signalement) {
      $io->section('📋 Tests avec signalement existant');

      $signalementTests = [
          'validated' => '✅ Signalement validé',
          'rejected' => '❌ Signalement rejeté',
          'status_update' => '🔄 Mise à jour statut',
          'resolved' => '🎉 Signalement résolu',
          'comment' => '💬 Nouveau commentaire'
      ];

      foreach ($signalementTests as $testKey => $testName) {
        try {
          switch ($testKey) {
            case 'validated':
              $this->emailService->sendSignalementValidatedEmail($signalement);
              break;

            case 'rejected':
              $this->emailService->sendSignalementRejectedEmail($signalement, 'Test de rejet');
              break;

            case 'status_update':
              $this->emailService->sendSignalementStatusUpdateEmail($signalement, 'en_attente', 'en_cours');
              break;

            case 'resolved':
              $this->emailService->sendSignalementResolvedEmail($signalement);
              break;

            case 'comment':
              $this->emailService->sendSignalementCommentEmail($signalement, 'Test commentaire', 'Admin Test');
              break;
          }

          $io->success("✅ {$testName} envoyé avec succès !");
          $successCount++;

        } catch (\Exception $e) {
          $io->error("❌ Échec {$testName} : " . $e->getMessage());
        }
      }
    }

    // Résumé
    $io->section('📊 Résumé des tests');
    $totalTests = count($tests) + ($signalement ? 5 : 0);

    if ($successCount === $totalTests) {
      $io->success("🎉 Tous les tests réussis ({$successCount}/{$totalTests}) ! Brevo est entièrement opérationnel !");
      $io->note('📬 Vérifiez votre boîte mail pour voir tous les emails reçus.');

    } elseif ($successCount > 0) {
      $io->warning("⚠️ {$successCount}/{$totalTests} tests réussis.");

    } else {
      $io->error('❌ Aucun test réussi.');
    }

    return $successCount > 0 ? Command::SUCCESS : Command::FAILURE;
  }

  private function createTestUser(): Utilisateur
  {
    $utilisateur = new Utilisateur();
    $utilisateur->setEmail('koutikaangemarie@gmail.com');
    $utilisateur->setNom('Test');
    $utilisateur->setPrenom('CityFlow');

    return $utilisateur;
  }
}