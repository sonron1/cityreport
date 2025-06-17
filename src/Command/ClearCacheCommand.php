<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

#[AsCommand(
    name: 'app:cache:clear-custom',
    description: 'Nettoie les caches de l\'application'
)]
class ClearCacheCommand extends Command
{
  protected function configure(): void
  {
    $this
        ->addOption('signalements', null, InputOption::VALUE_NONE, 'Nettoyer le cache des signalements')
        ->addOption('statistics', null, InputOption::VALUE_NONE, 'Nettoyer le cache des statistiques')
        ->addOption('all', null, InputOption::VALUE_NONE, 'Nettoyer tous les caches');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);
    $io->title('🧹 Nettoyage du cache');

    try {
      if ($input->getOption('all') || (!$input->getOption('signalements') && !$input->getOption('statistics'))) {
        // Nettoyer tous les pools de cache
        $this->clearAllCachePools($io);
      } else {
        if ($input->getOption('signalements')) {
          $this->clearSpecificPool('cache.signalements', 'Signalements', $io);
        }
        if ($input->getOption('statistics')) {
          $this->clearSpecificPool('cache.statistics', 'Statistiques', $io);
        }
      }

      $io->success('✅ Nettoyage terminé !');
      return Command::SUCCESS;

    } catch (\Exception $e) {
      $io->error('❌ Erreur : ' . $e->getMessage());
      return Command::FAILURE;
    }
  }

  private function clearAllCachePools(SymfonyStyle $io): void
  {
    $pools = [
        'cache.signalements' => 'Signalements',
        'cache.statistics' => 'Statistiques',
        'cache.api_results' => 'API Results',
        'cache.user_data' => 'Données utilisateur',
        'cache.geolocation' => 'Géolocalisation'
    ];

    $results = [];
    foreach ($pools as $poolName => $description) {
      try {
        $adapter = new FilesystemAdapter($poolName);
        $cleared = $adapter->clear();
        $results[] = [$description, $cleared ? '✅ Nettoyé' : '❌ Erreur'];
      } catch (\Exception $e) {
        $results[] = [$description, '❌ Erreur: ' . $e->getMessage()];
      }
    }

    $io->table(['Cache', 'Statut'], $results);
  }

  private function clearSpecificPool(string $poolName, string $description, SymfonyStyle $io): void
  {
    try {
      $adapter = new FilesystemAdapter($poolName);
      $result = $adapter->clear();
      $io->success($result ? "✅ Cache {$description} nettoyé" : "❌ Erreur nettoyage {$description}");
    } catch (\Exception $e) {
      $io->error("❌ Erreur {$description}: " . $e->getMessage());
    }
  }
}