<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class MonitoringService
{
  public function __construct(
      private LoggerInterface $logger
  ) {}

  /**
   * ✅ Monitoring des performances
   */
  public function trackPerformance(string $operation, callable $callback): mixed
  {
    $startTime = microtime(true);
    $startMemory = memory_get_usage(true);

    try {
      $result = $callback();

      $this->logPerformance($operation, $startTime, $startMemory, 'success');

      return $result;
    } catch (\Exception $e) {
      $this->logPerformance($operation, $startTime, $startMemory, 'error', $e);
      throw $e;
    }
  }

  /**
   * ✅ Log des métriques
   */
  private function logPerformance(
      string $operation,
      float $startTime,
      int $startMemory,
      string $status,
      \Exception $exception = null
  ): void {
    $duration = microtime(true) - $startTime;
    $memoryUsed = memory_get_usage(true) - $startMemory;

    $context = [
        'operation' => $operation,
        'duration' => round($duration * 1000, 2) . 'ms',
        'memory' => round($memoryUsed / 1024 / 1024, 2) . 'MB',
        'status' => $status
    ];

    if ($exception) {
      $context['exception'] = $exception->getMessage();
    }

    // Alert si performance dégradée
    if ($duration > 1.0) { // > 1 seconde
      $this->logger->warning('Performance dégradée', $context);
    } else {
      $this->logger->info('Performance tracking', $context);
    }
  }

  /**
   * ✅ Détection d'anomalies
   */
  public function detectAnomalies(Request $request): array
  {
    $anomalies = [];

    // Requêtes suspectes
    if (preg_match('/union|select|drop|delete|insert|update/i', $request->getQueryString() ?? '')) {
      $anomalies[] = 'SQL injection attempt detected';
    }

    // User-Agent suspect
    $userAgent = $request->headers->get('User-Agent', '');
    if (empty($userAgent) || preg_match('/bot|crawler|spider/i', $userAgent)) {
      $anomalies[] = 'Suspicious user agent';
    }

    // Log des anomalies
    if (!empty($anomalies)) {
      $this->logger->alert('Anomalies détectées', [
          'ip' => $request->getClientIp(),
          'anomalies' => $anomalies,
          'request' => $request->getRequestUri()
      ]);
    }

    return $anomalies;
  }
}