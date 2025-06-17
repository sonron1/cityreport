<?php

namespace App\Command;

use App\Entity\Utilisateur;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-notifications',
    description: 'Test le système de notifications'
)]
class TestNotificationsCommand extends Command
{
  public function __construct(
      private EntityManagerInterface $entityManager,
      private NotificationService $notificationService
  ) {
    parent::__construct();
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    try {
      // 1. Trouver un utilisateur de test
      $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->findOneBy([]);

      if (!$utilisateur) {
        $io->error('Aucun utilisateur trouvé dans la base de données');
        return Command::FAILURE;
      }

      $io->info("Test avec l'utilisateur : {$utilisateur->getEmail()}");

      // 2. Créer une notification de test
      $notification = $this->notificationService->creerNotification(
          $utilisateur,
          'Ceci est une notification de test du système ! 🚀',
          'information'
      );

      $io->success("✅ Notification #{$notification->getId()} créée avec succès !");

      // 3. Vérifier les notifications non lues
      $countUnread = $this->notificationService->compterNotificationsNonLues($utilisateur);
      $io->info("📊 Notifications non lues pour cet utilisateur : {$countUnread}");

      // 4. Lister les dernières notifications
      $notifications = $this->notificationService->getNotificationsNonLues($utilisateur);

      $io->section('Dernières notifications :');
      foreach ($notifications as $notif) {
        $io->writeln("• [{$notif->getType()}] {$notif->getMessage()} - {$notif->getDateEnvoi()->format('d/m/Y H:i')}");
      }

      // 5. Test marquer comme lu
      $this->notificationService->marquerCommeLue($notification);
      $io->success('✅ Notification marquée comme lue !');

      return Command::SUCCESS;

    } catch (\Exception $e) {
      $io->error('Erreur : ' . $e->getMessage());
      return Command::FAILURE;
    }
  }
}