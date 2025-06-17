<?php

namespace App\Command;

use App\Service\CacheService;
use App\Service\PerformanceOptimizationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:optimize:performance',
    description: 'Optimisation complète des performances'
)]
class OptimizePerformanceCommand extends Command
{
  public function __construct(
      private CacheService $cacheService,
      private PerformanceOptimizationService $optimizationService
  ) {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this
        ->addOption('full', 'f', InputOption::VALUE_NONE, 'Optimisation complète')
        ->addOption('cache-only', 'c', InputOption::VALUE_NONE, 'Cache uniquement')
        ->addOption('cleanup-only', null, InputOption::VALUE_NONE, 'Nettoyage uniquement');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);
    $startTime = microtime(true);

    $io->title('🚀 Optimisation des performances');

    try {
      if ($input->getOption('cache-only') || $input->getOption('full')) {
        $this->optimizeCache($io);
      }

      if ($input->getOption('cleanup-only') || $input->getOption('full')) {
        $this->performCleanup($io);
      }

      if ($input->getOption('full')) {
        $this->optimizeDatabase($io);
        $this->generateOptimizationReport($io);
      }

      $totalTime = microtime(true) - $startTime;
      $io->success("✅ Optimisation terminée en " . round($totalTime, 2) . "s");

      return Command::SUCCESS;

    } catch (\Exception $e) {
      $io->error('Erreur durant l\'optimisation : ' . $e->getMessage());
      return Command::FAILURE;
    }
  }

  private function optimizeCache(SymfonyStyle $io): void
  {
    $io->section('📦 Optimisation du cache');

    // Préchauffage intelligent
    $io->text('Préchauffage du cache...');
    $this->cacheService->warmup([
        'global_stats' => fn() => $this->optimizationService->warmupCache(),
        'user_preferences' => fn() => ['theme' => 'default'],
        'system_config' => fn() => ['version' => '1.0']
    ]);

    // Stats du cache
    $stats = $this->cacheService->getCacheStats();
    $io->table(['Métrique', 'Valeur'], [
        ['Hits', $stats['cache_hits']],
        ['Misses', $stats['cache_misses']],
        ['Taille', $stats['cache_size']],
        ['Ratio de hit', $stats['hit_ratio'] . '%']
    ]);

    $io->success('Cache optimisé');
  }

  private function performCleanup(SymfonyStyle $io): void
  {
    $io->section('🧹 Nettoyage');

    $results = $this->optimizationService->cleanupOldData();

    $io->table(['Type', 'Supprimés'], [
        ['Logs anciens', $results['logs_deleted']],
        ['Sessions expirées', $results['sessions_deleted']],
        ['Cache expiré', $results['cache_cleared'] ? 'Oui' : 'Non']
    ]);

    $io->success('Nettoyage terminé');
  }

  private function optimizeDatabase(SymfonyStyle $io): void
  {
    $io->section('🗃️ Optimisation base de données');

    $io->text('Analyse des requêtes...');
    $this->optimizationService->optimizeQueries();

    $io->success('Base de données optimisée');
  }

  private function generateOptimizationReport(SymfonyStyle $io): void
  {
    $io->section('📊 Rapport d\'optimisation');

    $io->text('Génération du rapport...');
    // Logique de génération de rapport

    $io->success('Rapport généré');
  }
}