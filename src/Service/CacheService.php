<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Psr\Log\LoggerInterface;

class CacheService
{
  private const DEFAULT_TTL = 3600; // 1 heure
  private const LONG_TTL = 86400;   // 24 heures
  private const SHORT_TTL = 300;    // 5 minutes

  public function __construct(
      private CacheInterface $cache,
      private LoggerInterface $logger
  ) {}

  /**
   * ✅ Cache avec invalidation intelligente
   */
  public function getOrSet(string $key, callable $callback, int $ttl = self::DEFAULT_TTL): mixed
  {
    try {
      return $this->cache->get($key, function (ItemInterface $item) use ($callback, $ttl) {
        $item->expiresAfter($ttl);

        $startTime = microtime(true);
        $result = $callback();
        $duration = microtime(true) - $startTime;

        $this->logger->info('Cache miss pour clé: ' . $item->getKey(), [
            'duration' => $duration,
            'memory' => memory_get_usage(true)
        ]);

        return $result;
      });
    } catch (\Exception $e) {
      $this->logger->error('Erreur cache pour clé: ' . $key, [
          'exception' => $e->getMessage()
      ]);

      // Fallback : exécuter directement
      return $callback();
    }
  }

  /**
   * ✅ Cache pour les statistiques
   */
  public function getStats(string $key, callable $callback): array
  {
    return $this->getOrSet('stats_' . $key, $callback, self::LONG_TTL);
  }

  /**
   * ✅ Cache pour les données fréquemment consultées
   */
  public function getFrequentData(string $key, callable $callback): mixed
  {
    return $this->getOrSet('frequent_' . $key, $callback, self::SHORT_TTL);
  }

  /**
   * ✅ Invalidation par tag
   */
  public function invalidateByTag(string $tag): void
  {
    try {
      $this->cache->delete($tag);
      $this->logger->info('Cache invalidé pour tag: ' . $tag);
    } catch (\Exception $e) {
      $this->logger->error('Erreur invalidation cache: ' . $tag, [
          'exception' => $e->getMessage()
      ]);
    }
  }

  /**
   * ✅ Préchargement du cache
   */
  public function warmup(array $keys): void
  {
    foreach ($keys as $key => $callback) {
      $this->getOrSet($key, $callback);
    }

    $this->logger->info('Cache préchauffé', ['keys' => array_keys($keys)]);
  }
}