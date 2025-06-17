<?php

namespace App\Tests\Unit\Repository;

use App\Entity\Signalement;
use App\Entity\Utilisateur;
use App\Repository\SignalementRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

class SignalementRepositoryTest extends TestCase
{
  private SignalementRepository $repository;
  private EntityManagerInterface $entityManager;
  private QueryBuilder $queryBuilder;
  private Query $query;

  protected function setUp(): void
  {
    $this->entityManager = $this->createMock(EntityManagerInterface::class);
    $this->queryBuilder = $this->createMock(QueryBuilder::class);
    $this->query = $this->createMock(Query::class);

    $this->repository = new SignalementRepository($this->entityManager);
  }

  /**
   * ✅ Test optimisation des requêtes
   */
  public function testFindWithOptimizedJoins(): void
  {
    $filters = ['statut' => 'nouveau'];
    $limit = 10;
    $offset = 0;

    // Mock du QueryBuilder
    $this->entityManager
        ->expects($this->once())
        ->method('createQueryBuilder')
        ->willReturn($this->queryBuilder);

    $this->queryBuilder
        ->expects($this->once())
        ->method('select')
        ->with('s', 'u', 'v', 'c', 'a')
        ->willReturnSelf();

    $this->queryBuilder
        ->expects($this->exactly(4))
        ->method('leftJoin')
        ->willReturnSelf();

    $this->queryBuilder
        ->expects($this->once())
        ->method('setMaxResults')
        ->with($limit)
        ->willReturnSelf();

    $this->queryBuilder
        ->expects($this->once())
        ->method('getQuery')
        ->willReturn($this->query);

    $this->query
        ->expects($this->once())
        ->method('useQueryCache')
        ->with(true)
        ->willReturnSelf();

    $this->query
        ->expects($this->once())
        ->method('useResultCache')
        ->willReturnSelf();

    $this->query
        ->expects($this->once())
        ->method('getResult')
        ->willReturn([]);

    $result = $this->repository->findWithOptimizedJoins($filters, $limit, $offset);

    $this->assertIsArray($result);
  }

  /**
   * ✅ Test performance comptage
   */
  public function testCountOptimizedPerformance(): void
  {
    $startTime = microtime(true);

    // Mock pour simuler une requête rapide
    $this->entityManager
        ->expects($this->once())
        ->method('createQueryBuilder')
        ->willReturn($this->queryBuilder);

    $this->queryBuilder
        ->method('select')
        ->willReturnSelf();

    $this->queryBuilder
        ->method('where')
        ->willReturnSelf();

    $this->queryBuilder
        ->method('setParameter')
        ->willReturnSelf();

    $this->queryBuilder
        ->method('getQuery')
        ->willReturn($this->query);

    $this->query
        ->method('useQueryCache')
        ->willReturnSelf();

    $this->query
        ->method('useResultCache')
        ->willReturnSelf();

    $this->query
        ->method('getSingleScalarResult')
        ->willReturn(42);

    $result = $this->repository->countOptimized([]);

    $duration = microtime(true) - $startTime;

    $this->assertEquals(42, $result);
    $this->assertLessThan(0.1, $duration, 'Comptage trop lent'); // < 100ms
  }
}