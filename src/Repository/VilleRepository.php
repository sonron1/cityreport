<?php

namespace App\Repository;

use App\Entity\Ville;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * @extends ServiceEntityRepository<Ville>
 */
class VilleRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Ville::class);
  }

  /**
   * Trouve les villes avec pagination
   */
  public function findPaginated(int $page = 1, int $limit = 12): Paginator
  {
    $offset = ($page - 1) * $limit;

    $query = $this->createQueryBuilder('v')
        ->leftJoin('v.signalements', 's')
        ->addSelect('COUNT(s.id) as HIDDEN signalement_count')
        ->groupBy('v.id, v.nom') // ✅ CORRECTION : Ajouter v.nom dans le GROUP BY
        ->orderBy('v.nom', 'ASC')
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery();

    $doctrinePaginator = new DoctrinePaginator($query);

    return new Paginator($doctrinePaginator, $page, $limit);
  }

  /**
   * Trouve les villes avec le nombre de signalements
   */
  public function findWithSignalementCount(): array
  {
    return $this->createQueryBuilder('v')
        ->leftJoin('v.signalements', 's')
        ->addSelect('COUNT(s.id) as signalement_count')
        ->groupBy('v.id, v.nom')
        ->orderBy('v.nom', 'ASC')
        ->getQuery()
        ->getResult();
  }

  /**
   * Trouve les villes avec le nombre de signalements et informations du département
   * Corrige l'erreur de GROUP BY pour PostgreSQL
   */
  public function findWithSignalementAndDepartementCount(): array
  {
    return $this->createQueryBuilder('v')
        ->leftJoin('v.departement', 'd')
        ->leftJoin('v.signalements', 's')
        ->addSelect('COUNT(s.id) as nb_signalements')
        ->addSelect('d.nom as departement_nom')
        ->addSelect('d.id as departement_id')
        ->groupBy('v.id, v.nom, d.id, d.nom') // Inclure toutes les colonnes non-agrégées
        ->orderBy('v.nom', 'ASC')
        ->getQuery()
        ->getResult();
  }

  /**
   * Recherche des villes par nom
   */
  public function findByName(string $searchTerm): array
  {
    return $this->createQueryBuilder('v')
        ->where('v.nom LIKE :term')
        ->setParameter('term', '%' . $searchTerm . '%')
        ->orderBy('v.nom', 'ASC')
        ->getQuery()
        ->getResult();
  }

  /**
   * Compte le nombre total de villes
   */
  public function countTotal(): int
  {
    return $this->createQueryBuilder('v')
        ->select('COUNT(v.id)')
        ->getQuery()
        ->getSingleScalarResult();
  }

  /**
   * Trouve une ville par son slug
   */
  public function findOneBySlug(string $slug): ?Ville
  {
    return $this->createQueryBuilder('v')
        ->where('v.slug = :slug')
        ->setParameter('slug', $slug)
        ->getQuery()
        ->getOneOrNullResult();
  }

  /**
   * Trouve les villes d'un département
   */
  public function findByDepartement(int $departementId): array
  {
    return $this->createQueryBuilder('v')
        ->where('v.departement = :departement')
        ->setParameter('departement', $departementId)
        ->orderBy('v.nom', 'ASC')
        ->getQuery()
        ->getResult();
  }

  /**
   * Trouve les villes avec leurs signalements actifs
   */
  public function findWithActiveSignalements(): array
  {
    return $this->createQueryBuilder('v')
        ->leftJoin('v.signalements', 's')
        ->where('s.etatValidation = :etat OR s.etatValidation IS NULL')
        ->setParameter('etat', 'valide')
        ->groupBy('v.id')
        ->having('COUNT(s.id) > 0')
        ->orderBy('v.nom', 'ASC')
        ->getQuery()
        ->getResult();
  }

  /**
   * Trouve toutes les villes du Bénin
   * Cette méthode est utilisée dans le formulaire de signalements
   */
  public function findVillesDuBenin(): array
  {
    return $this->createQueryBuilder('v')
        ->leftJoin('v.departement', 'd')
        ->addSelect('d')
        ->orderBy('v.nom', 'ASC')
        ->getQuery()
        ->getResult();
  }

  /**
   * Trouve les villes par département (alternative)
   */
  public function findByDepartementName(string $departementName): array
  {
    return $this->createQueryBuilder('v')
        ->leftJoin('v.departement', 'd')
        ->where('d.nom = :departement')
        ->setParameter('departement', $departementName)
        ->orderBy('v.nom', 'ASC')
        ->getQuery()
        ->getResult();
  }

  /**
   * Trouve toutes les villes ordonnées par nom
   */
  public function findAllOrdered(): array
  {
    return $this->findBy([], ['nom' => 'ASC']);
  }
}