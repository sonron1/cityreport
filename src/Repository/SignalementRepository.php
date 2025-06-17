<?php

namespace App\Repository;

use App\Entity\Signalement;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Enum\EtatValidation;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

class SignalementRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Signalement::class);
  }

  // ✅ MÉTHODES AVEC CLASSE PAGINATOR CORRIGÉES

  /**
   * Paginer les signalements de l'utilisateur
   */
  public function findUserSignalementsPaginated(
      Utilisateur $user,
      int $page = 1,
      int $limit = 9,
      array $filters = []
  ): Paginator {
    $qb = $this->createQueryBuilder('s')
        ->select('s', 'v', 'c', 'a')
        ->leftJoin('s.ville', 'v')
        ->leftJoin('s.categorie', 'c')
        ->leftJoin('s.arrondissement', 'a')
        ->where('s.utilisateur = :user')
        ->setParameter('user', $user)
        ->orderBy('s.dateSignalement', 'DESC');

    $this->applyFilters($qb, $filters);

    $offset = ($page - 1) * $limit;
    $qb->setFirstResult($offset)->setMaxResults($limit);

    // ✅ CORRECTION : Passer directement le DoctrinePaginator
    $doctrinePaginator = new DoctrinePaginator($qb->getQuery());

    return new Paginator($doctrinePaginator, $page, $limit);
  }

  /**
   * Paginer TOUS les signalements (pour modérateurs)
   */
  public function findAllSignalementsPaginated(
      int $page = 1,
      int $limit = 9,
      array $filters = [],
      bool $includeNonValidated = false
  ): Paginator {
    $qb = $this->createQueryBuilder('s')
        ->select('s', 'u', 'v', 'c', 'a')
        ->leftJoin('s.utilisateur', 'u')
        ->leftJoin('s.ville', 'v')
        ->leftJoin('s.categorie', 'c')
        ->leftJoin('s.arrondissement', 'a')
        ->orderBy('s.dateSignalement', 'DESC');

    if (!$includeNonValidated) {
      $qb->andWhere('s.etatValidation = :etat')
          ->setParameter('etat', EtatValidation::VALIDE->value);
    }

    $this->applyFilters($qb, $filters);

    $offset = ($page - 1) * $limit;
    $qb->setFirstResult($offset)->setMaxResults($limit);

    // ✅ CORRECTION : Passer directement le DoctrinePaginator
    $doctrinePaginator = new DoctrinePaginator($qb->getQuery());

    return new Paginator($doctrinePaginator, $page, $limit);
  }

  /**
   * Paginer les signalements publics (pour utilisateurs normaux)
   */
  public function findAllPublicSignalementsPaginated(
      int $page = 1,
      int $limit = 9,
      array $filters = []
  ): Paginator {
    return $this->findAllSignalementsPaginated($page, $limit, $filters, false);
  }

  /**
   * Compter les signalements par statut pour un utilisateur
   */
  public function countUserSignalementsByStatus(Utilisateur $user, string $status): int
  {
    return $this->createQueryBuilder('s')
        ->select('COUNT(s.id)')
        ->where('s.utilisateur = :user')
        ->andWhere('s.statut = :status')
        ->setParameter('user', $user)
        ->setParameter('status', $status)
        ->getQuery()
        ->getSingleScalarResult();
  }

  /**
   * ✅ OPTIMISÉ : Pagination avec comptage efficace
   */
  public function findPaginatedOptimized(array $filters = [], int $page = 1, int $limit = 10): Paginator
  {
    // Requête principale avec LIMIT/OFFSET
    $qb = $this->createQueryBuilder('s')
        ->select('s', 'u', 'v', 'c')
        ->leftJoin('s.utilisateur', 'u')
        ->leftJoin('s.ville', 'v')
        ->leftJoin('s.categorie', 'c')
        ->where('s.etatValidation = :etat')
        ->setParameter('etat', EtatValidation::VALIDE->value)
        ->orderBy('s.dateSignalement', 'DESC');

    $this->applyOptimizedFilters($qb, $filters);

    // Pagination optimisée
    $offset = ($page - 1) * $limit;
    $qb->setFirstResult($offset)->setMaxResults($limit);

    // ✅ CORRECTION : Utiliser DoctrinePaginator directement
    $doctrinePaginator = new DoctrinePaginator($qb->getQuery());

    return new Paginator($doctrinePaginator, $page, $limit);
  }

  /**
   * Appliquer les filtres à la requête
   */
  private function applyFilters(QueryBuilder $qb, array $filters): void
  {
    if (!empty($filters['statut'])) {
      $qb->andWhere('s.statut = :statut')
          ->setParameter('statut', $filters['statut']);
    }

    if (!empty($filters['categorie']) && is_numeric($filters['categorie'])) {
      $qb->andWhere('s.categorie = :categorieId')
          ->setParameter('categorieId', $filters['categorie']);
    }

    if (!empty($filters['ville']) && is_numeric($filters['ville'])) {
      $qb->andWhere('s.ville = :villeId')
          ->setParameter('villeId', $filters['ville']);
    }

    if (!empty($filters['date'])) {
      $this->applyDateFilter($qb, $filters['date']);
    }
  }

  /**
   * Appliquer un filtre de date
   */
  private function applyDateFilter(QueryBuilder $qb, string $dateFilter): void
  {
    $now = new \DateTime();

    switch ($dateFilter) {
      case 'today':
        $qb->andWhere('s.dateSignalement >= :today')
            ->setParameter('today', $now->format('Y-m-d 00:00:00'));
        break;
      case 'week':
        $qb->andWhere('s.dateSignalement >= :week')
            ->setParameter('week', $now->modify('-7 days')->format('Y-m-d 00:00:00'));
        break;
      case 'month':
        $qb->andWhere('s.dateSignalement >= :month')
            ->setParameter('month', $now->format('Y-m-01 00:00:00'));
        break;
    }
  }

  // ✅ MÉTHODES EXISTANTES CONSERVÉES

  /**
   * ✅ NOUVELLE MÉTHODE : Nécessaire pour l'API
   */
  public function findByFilters(array $filters = []): array
  {
    $qb = $this->createQueryBuilder('s')
        ->select('s', 'u', 'v', 'c')
        ->leftJoin('s.utilisateur', 'u')
        ->leftJoin('s.ville', 'v')
        ->leftJoin('s.categorie', 'c')
        ->orderBy('s.dateSignalement', 'DESC');

    // Filtre par état de validation
    if (!empty($filters['etat'])) {
      $qb->andWhere('s.etatValidation = :etat')
          ->setParameter('etat', $filters['etat']);
    } else {
      // Par défaut, seulement les signalements validés
      $qb->andWhere('s.etatValidation = :defaultEtat')
          ->setParameter('defaultEtat', EtatValidation::VALIDE->value);
    }

    // Filtre par ville
    if (!empty($filters['ville']) && is_numeric($filters['ville'])) {
      $qb->andWhere('s.ville = :villeId')
          ->setParameter('villeId', $filters['ville']);
    }

    // Filtre par catégorie
    if (!empty($filters['categorie']) && is_numeric($filters['categorie'])) {
      $qb->andWhere('s.categorie = :categorieId')
          ->setParameter('categorieId', $filters['categorie']);
    }

    // Filtre par date (du)
    if (!empty($filters['date_du'])) {
      $qb->andWhere('s.dateSignalement >= :dateDu')
          ->setParameter('dateDu', $filters['date_du']);
    }

    // Filtre par date (au)
    if (!empty($filters['date_au'])) {
      $qb->andWhere('s.dateSignalement <= :dateAu')
          ->setParameter('dateAu', $filters['date_au']);
    }

    return $qb->getQuery()->getResult();
  }

  /**
   * ✅ OPTIMISÉ : Récupérer signalements avec jointures optimisées
   */
  public function findWithOptimizedJoins(array $filters = [], int $limit = 10, int $offset = 0): array
  {
    $qb = $this->createQueryBuilder('s')
        ->select('s', 'u', 'v', 'c', 'a')
        ->leftJoin('s.utilisateur', 'u')
        ->leftJoin('s.ville', 'v')
        ->leftJoin('s.categorie', 'c')
        ->leftJoin('s.arrondissement', 'a')
        ->where('s.etatValidation = :etat')
        ->setParameter('etat', EtatValidation::VALIDE->value)
        ->orderBy('s.dateSignalement', 'DESC')
        ->setMaxResults($limit)
        ->setFirstResult($offset);

    $this->applyOptimizedFilters($qb, $filters);

    return $qb->getQuery()
        ->useQueryCache(true)
        ->useResultCache(true, 300, 'signalements_' . md5(serialize($filters)))
        ->getResult();
  }

  /**
   * ✅ OPTIMISÉ : Comptage sans jointures inutiles
   */
  public function countOptimized(array $filters = []): int
  {
    $qb = $this->createQueryBuilder('s')
        ->select('COUNT(s.id)')
        ->where('s.etatValidation = :etat')
        ->setParameter('etat', EtatValidation::VALIDE->value);

    $this->applyOptimizedFilters($qb, $filters, false);

    return (int) $qb->getQuery()
        ->useQueryCache(true)
        ->useResultCache(true, 600)
        ->getSingleScalarResult();
  }

  /**
   * ✅ OPTIMISÉ : Statistiques en une seule requête
   */
  public function getStatisticsOptimized(Utilisateur $user = null): array
  {
    $qb = $this->createQueryBuilder('s')
        ->select('
                COUNT(s.id) as total,
                COUNT(CASE WHEN s.statut = :nouveau THEN 1 END) as nouveau,
                COUNT(CASE WHEN s.statut = :en_cours THEN 1 END) as en_cours,
                COUNT(CASE WHEN s.statut = :resolu THEN 1 END) as resolu,
                COUNT(CASE WHEN s.etatValidation = :en_attente THEN 1 END) as en_attente
            ')
        ->setParameter('nouveau', 'nouveau')
        ->setParameter('en_cours', 'en_cours')
        ->setParameter('resolu', 'resolu')
        ->setParameter('en_attente', EtatValidation::EN_ATTENTE->value);

    if ($user) {
      $qb->where('s.utilisateur = :user')
          ->setParameter('user', $user);
    }

    return $qb->getQuery()
        ->useQueryCache(true)
        ->useResultCache(true, 600, 'stats_' . ($user ? $user->getId() : 'global'))
        ->getSingleResult();
  }

  /**
   * ✅ OPTIMISÉ : Recherche avec index
   */
  public function searchOptimized(string $query, int $limit = 20): array
  {
    return $this->createQueryBuilder('s')
        ->select('s', 'v', 'c')
        ->leftJoin('s.ville', 'v')
        ->leftJoin('s.categorie', 'c')
        ->where('s.etatValidation = :etat')
        ->andWhere('(
                s.titre LIKE :query OR 
                s.description LIKE :query OR 
                v.nom LIKE :query OR 
                c.nom LIKE :query
            )')
        ->setParameter('etat', EtatValidation::VALIDE->value)
        ->setParameter('query', '%' . $query . '%')
        ->orderBy('s.dateSignalement', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->useQueryCache(true)
        ->useResultCache(true, 300)
        ->getResult();
  }

  /**
   * ✅ OPTIMISÉ : Filtres sans jointures inutiles
   */
  private function applyOptimizedFilters(QueryBuilder $qb, array $filters, bool $withJoins = true): void
  {
    if (!empty($filters['statut'])) {
      $qb->andWhere('s.statut = :statut')
          ->setParameter('statut', $filters['statut']);
    }

    if (!empty($filters['ville']) && is_numeric($filters['ville'])) {
      $qb->andWhere('s.ville = :villeId')
          ->setParameter('villeId', $filters['ville']);
    }

    if (!empty($filters['categorie']) && is_numeric($filters['categorie'])) {
      $qb->andWhere('s.categorie = :categorieId')
          ->setParameter('categorieId', $filters['categorie']);
    }

    if (!empty($filters['date'])) {
      $this->applyDateFilter($qb, $filters['date']);
    }
  }

  /**
   * ✅ PERFORMANCE : Nettoyage des anciennes données
   */
  public function cleanupOldData(int $days = 365): int
  {
    $date = new \DateTime("-{$days} days");

    return $this->getEntityManager()
        ->createQuery('
                DELETE FROM App\Entity\Signalement s 
                WHERE s.dateSignalement < :date 
                AND s.statut = :statut
            ')
        ->setParameter('date', $date)
        ->setParameter('statut', 'annule')
        ->execute();
  }
}