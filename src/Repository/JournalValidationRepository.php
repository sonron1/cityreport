<?php

namespace App\Repository;

use App\Entity\JournalValidation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class JournalValidationRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, JournalValidation::class);
  }

  /**
   * ✅ Méthode PostgreSQL-compatible pour les validations avec filtres
   */
  public function findWithFilters(array $filters = [], int $page = 1, int $limit = 20): array
  {
    $qb = $this->createQueryBuilder('j')
        ->leftJoin('j.signalement', 's')
        ->leftJoin('j.moderateur', 'm')
        ->addSelect('s', 'm');

    // Appliquer les filtres
    if (!empty($filters['search'])) {
        $qb->andWhere('
            s.titre LIKE :search OR 
            j.commentaire LIKE :search OR 
            CONCAT(m.prenom, \' \', m.nom) LIKE :search
        ')->setParameter('search', '%' . $filters['search'] . '%');
    }

    if (!empty($filters['action_filter'])) {
        $action = $filters['action_filter'];
        if ($action === 'validated') {
            $qb->andWhere('j.action = :action')->setParameter('action', 'validé');
        } elseif ($action === 'rejected') {
            $qb->andWhere('j.action = :action')->setParameter('action', 'rejeté');
        } elseif ($action === 'pending') {
            $qb->andWhere('j.action = :action')->setParameter('action', 'en attente');
        } elseif ($action === 'modified') {
            $qb->andWhere('j.action LIKE :action')->setParameter('action', '%Modification%');
        }
    }

    if (!empty($filters['moderator_filter']) && is_numeric($filters['moderator_filter'])) {
        $qb->andWhere('j.moderateur = :moderateur')
            ->setParameter('moderateur', (int) $filters['moderator_filter']);
    }

    if (!empty($filters['date_from'])) {
        $qb->andWhere('j.dateValidation >= :dateFrom')
            ->setParameter('dateFrom', $filters['date_from']);
    }

    if (!empty($filters['date_to'])) {
        $qb->andWhere('j.dateValidation <= :dateTo')
            ->setParameter('dateTo', $filters['date_to']);
    }

    // Tri et pagination
    $qb->orderBy('j.dateValidation', 'DESC')
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit);

    return $qb->getQuery()->getResult();
  }

  /**
   * ✅ Compter avec filtres
   */
  public function countWithFilters(array $filters = []): int
  {
    $qb = $this->createQueryBuilder('j')
        ->select('COUNT(j.id)')
        ->leftJoin('j.signalement', 's')
        ->leftJoin('j.moderateur', 'm');

    // Appliquer les mêmes filtres que findWithFilters
    if (!empty($filters['search'])) {
        $qb->andWhere('
            s.titre LIKE :search OR 
            j.commentaire LIKE :search OR 
            CONCAT(m.prenom, \' \', m.nom) LIKE :search
        ')->setParameter('search', '%' . $filters['search'] . '%');
    }

    if (!empty($filters['action_filter'])) {
        $action = $filters['action_filter'];
        if ($action === 'validated') {
            $qb->andWhere('j.action = :action')->setParameter('action', 'validé');
        } elseif ($action === 'rejected') {
            $qb->andWhere('j.action = :action')->setParameter('action', 'rejeté');
        } elseif ($action === 'pending') {
            $qb->andWhere('j.action = :action')->setParameter('action', 'en attente');
        } elseif ($action === 'modified') {
            $qb->andWhere('j.action LIKE :action')->setParameter('action', '%Modification%');
        }
    }

    if (!empty($filters['moderator_filter']) && is_numeric($filters['moderator_filter'])) {
        $qb->andWhere('j.moderateur = :moderateur')
            ->setParameter('moderateur', (int) $filters['moderator_filter']);
    }

    if (!empty($filters['date_from'])) {
        $qb->andWhere('j.dateValidation >= :dateFrom')
            ->setParameter('dateFrom', $filters['date_from']);
    }

    if (!empty($filters['date_to'])) {
        $qb->andWhere('j.dateValidation <= :dateTo')
            ->setParameter('dateTo', $filters['date_to']);
    }

    return (int) $qb->getQuery()->getSingleScalarResult();
  }
}