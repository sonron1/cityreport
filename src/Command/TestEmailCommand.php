<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

#[AsCommand(
    name: 'app:test-email',
    description: 'Test simple d\'envoi d\'email via Brevo avec Symfony Mailer',
)]
class TestEmailCommand extends Command
{
  public function __construct(
      private MailerInterface $mailer,
      private string $emailSender
  ) {
    parent::__construct();
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    $io->title('📧 Test d\'envoi d\'email via Brevo');

    // Configuration des tests
    $tests = [
        [
            'to' => 'koutikaangemarie@gmail.com',
            'description' => 'Test 1: Email principal'
        ],
        [
            'to' => 'angepop1998@gmail.com',
            'description' => 'Test 2: Email secondaire'
        ]
    ];

    $successCount = 0;
    $totalTests = count($tests);

    foreach ($tests as $index => $test) {
      $testNumber = $index + 1;

      $io->section($test['description']);
      $io->text("📤 Expéditeur: {$this->emailSender}");
      $io->text("📬 Destinataire: {$test['to']}");
      $io->text('🔄 Envoi en cours...');

      try {
        $email = (new Email())
            ->from(new Address($this->emailSender, 'CityFlow Bénin'))
            ->to($test['to'])
            ->subject('🧪 Test CityFlow Bénin #' . $testNumber)
            ->html($this->createEmailTemplate($testNumber, $test));

        $this->mailer->send($email);

        $io->success("✅ Test #{$testNumber} : Email envoyé avec succès !");
        $successCount++;

      } catch (\Exception $e) {
        $io->error("❌ Test #{$testNumber} échoué : " . $e->getMessage());

        // Détails de l'erreur pour le debug
        $io->text("🔍 Type d'erreur : " . get_class($e));
        if (method_exists($e, 'getCode') && $e->getCode()) {
          $io->text("📋 Code erreur : " . $e->getCode());
        }
      }

      $io->newLine();
    }

    // Résumé final
    $this->displaySummary($io, $successCount, $totalTests);

    return $successCount > 0 ? Command::SUCCESS : Command::FAILURE;
  }

  private function createEmailTemplate(int $testNumber, array $test): string
  {
    return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; }
                .header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 14px; }
                .badge { background: #4CAF50; color: white; padding: 5px 10px; border-radius: 5px; display: inline-block; }
                .info-box { background: white; padding: 20px; border-radius: 8px; margin: 15px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .success { color: #4CAF50; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🏢 CityFlow Bénin</h1>
                    <p>Test d'email #{$testNumber} - Configuration Brevo</p>
                </div>
                
                <div class='content'>
                    <div class='info-box'>
                        <h2>🎉 Test réussi !</h2>
                        <p>Votre configuration email <strong>CityFlow Bénin</strong> via <strong>Brevo</strong> fonctionne parfaitement !</p>
                        
                        <h3>📋 Détails de ce test :</h3>
                        <ul>
                            <li><strong>Test :</strong> {$test['description']}</li>
                            <li><strong>Expéditeur :</strong> {$this->emailSender}</li>
                            <li><strong>Destinataire :</strong> {$test['to']}</li>
                            <li><strong>Statut :</strong> <span class='badge success'>✅ SUCCÈS</span></li>
                            <li><strong>Date/Heure :</strong> " . date('d/m/Y H:i:s') . "</li>
                        </ul>
                    </div>
                    
                    <div class='info-box'>
                        <h3>🚀 Votre plateforme est prête pour :</h3>
                        <ul>
                            <li>✅ Emails de bienvenue aux nouveaux utilisateurs</li>
                            <li>✅ Confirmations d'inscription</li>
                            <li>✅ Notifications de signalements</li>
                            <li>✅ Réinitialisation de mots de passe</li>
                            <li>✅ Mises à jour de statuts</li>
                        </ul>
                    </div>
                </div>
                
                <div class='footer'>
                    <p><strong>CityFlow Bénin</strong> - Plateforme de signalement citoyen</p>
                    <p>Email envoyé via Brevo SMTP • Test #{$testNumber}</p>
                </div>
            </div>
        </body>
        </html>";
  }

  private function displaySummary(SymfonyStyle $io, int $successCount, int $totalTests): void
  {
    $io->section('📊 Résumé des tests Brevo');

    $io->text("✅ Tests réussis: {$successCount}/{$totalTests}");

    if ($successCount === $totalTests) {
      $io->success('🎉 Parfait ! Brevo est entièrement configuré et opérationnel !');
      $io->text('🚀 Votre plateforme peut maintenant envoyer des emails.');
      $io->note('📬 Vérifiez vos boîtes mail (et dossiers spam) pour voir les emails reçus.');

    } elseif ($successCount > 0) {
      $io->warning("⚠️ {$successCount} test(s) sur {$totalTests} ont réussi.");
      $io->text('🔧 Vérifiez la configuration pour les échecs.');

    } else {
      $io->error('❌ Aucun test n\'a réussi.');
      $io->text('🔍 Points à vérifier :');
      $io->listing([
          'Vérifiez MAILER_DSN dans votre .env',
          'Vérifiez que EMAIL_FROM est une adresse validée dans Brevo',
          'Vérifiez vos identifiants SMTP Brevo',
          'Consultez les logs Brevo pour plus de détails'
      ]);
    }

    $io->note('💡 Consultez les logs Brevo : https://app.brevo.com/log/sms-email');
  }
}