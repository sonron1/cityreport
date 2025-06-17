<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class PerformanceOptimizationService
{
  public function __construct(
      private EntityManagerInterface $entityManager,
      private CacheInterface $cache,
      private LoggerInterface $logger
  ) {}

  public function optimizeQueries(): void
  {
    $startTime = microtime(true);
    // Logique d'optimisation des requêtes
    $this->logger->info('Optimisation requêtes terminée', [
        'duration' => microtime(true) - $startTime
    ]);
  }

  public function warmupCache(): array
  {
    $results = [];
    $startTime = microtime(true);

    // Cache des statistiques globales
    $results['stats'] = $this->cache->get('global_stats', function() {
      return $this->getGlobalStats();
    });

    // Cache des catégories populaires
    $results['categories'] = $this->cache->get('popular_categories', function() {
      return $this->getPopularCategories();
    });

    $this->logger->info('Cache warming completed', [
        'duration' => microtime(true) - $startTime
    ]);

    return $results;
  }

  public function cleanupOldData(): array
  {
    return [
        'logs_deleted' => $this->cleanupLogs(),
        'sessions_deleted' => 0,
        'cache_cleared' => $this->cache->clear()
    ];
  }

  private function getGlobalStats(): array
  {
    return [
        'total_signalements' => 100,
        'nouveaux' => 25,
        'en_cours' => 50,
        'resolus' => 25
    ];
  }

  private function getPopularCategories(): array
  {
    return $this->entityManager->createQuery('
            SELECT c.id, c.nom, COUNT(s.id) as signalement_count
            FROM App\Entity\Categorie c
            LEFT JOIN App\Entity\Signalement s WITH s.categorie = c
            GROUP BY c.id
            ORDER BY signalement_count DESC
        ')->setMaxResults(10)->getResult();
  }

  private function cleanupLogs(): int
  {
    return $this->entityManager->createQuery('
            DELETE FROM App\Entity\Log l 
            WHERE l.dateCreation < :date
        ')
        ->setParameter('date', new \DateTime('-30 days'))
        ->execute();
  }
}