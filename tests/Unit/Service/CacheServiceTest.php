<?php

namespace App\Tests\Unit\Service;

use App\Service\CacheService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CacheServiceTest extends TestCase
{
  private CacheService $cacheService;
  private CacheInterface $cache;
  private LoggerInterface $logger;

  protected function setUp(): void
  {
    $this->cache = $this->createMock(CacheInterface::class);
    $this->logger = $this->createMock(LoggerInterface::class);
    $this->cacheService = new CacheService($this->cache, $this->logger);
  }

  /**
   * ✅ Test cache hit
   */
  public function testCacheHit(): void
  {
    $key = 'test_key';
    $expectedResult = 'cached_value';

    $this->cache
        ->expects($this->once())
        ->method('get')
        ->with($key)
        ->willReturn($expectedResult);

    $result = $this->cacheService->getOrSet($key, function() {
      return 'should_not_be_called';
    });

    $this->assertEquals($expectedResult, $result);
  }

  /**
   * ✅ Test cache miss avec callback
   */
  public function testCacheMiss(): void
  {
    $key = 'test_key';
    $callbackResult = 'fresh_value';

    $item = $this->createMock(ItemInterface::class);
    $item->expects($this->once())
        ->method('expiresAfter')
        ->with(3600);

    $this->cache
        ->expects($this->once())
        ->method('get')
        ->with($key)
        ->willReturnCallback(function($key, $callback) use ($item) {
          return $callback($item);
        });

    $result = $this->cacheService->getOrSet($key, function() use ($callbackResult) {
      return $callbackResult;
    });

    $this->assertEquals($callbackResult, $result);
  }

  /**
   * ✅ Test gestion d'erreur
   */
  public function testCacheErrorFallback(): void
  {
    $key = 'test_key';
    $fallbackResult = 'fallback_value';

    $this->cache
        ->expects($this->once())
        ->method('get')
        ->willThrowException(new \Exception('Cache error'));

    $this->logger
        ->expects($this->once())
        ->method('error');

    $result = $this->cacheService->getOrSet($key, function() use ($fallbackResult) {
      return $fallbackResult;
    });

    $this->assertEquals($fallbackResult, $result);
  }
}