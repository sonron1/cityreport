<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Notification::class);
  }

  /**
   * Trouve les notifications non lues d'un utilisateur
   */
  public function findUnreadByUser(Utilisateur $utilisateur, int $limit = 10): array
  {
    return $this->createQueryBuilder('n')
        ->where('n.destinataire = :utilisateur')
        ->andWhere('n.statut = :statut')
        ->setParameter('utilisateur', $utilisateur)
        ->setParameter('statut', 'non_lue')
        ->orderBy('n.dateEnvoi', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
  }

  /**
   * Compte les notifications non lues d'un utilisateur
   */
  public function countUnreadByUser(Utilisateur $utilisateur): int
  {
    return $this->createQueryBuilder('n')
        ->select('COUNT(n.id)')
        ->where('n.destinataire = :utilisateur')
        ->andWhere('n.statut = :statut')
        ->setParameter('utilisateur', $utilisateur)
        ->setParameter('statut', 'non_lue')
        ->getQuery()
        ->getSingleScalarResult();
  }

  /**
   * Trouve les notifications récentes d'un utilisateur
   */
  public function findRecentByUser(Utilisateur $utilisateur, int $limit = 20): array
  {
    return $this->createQueryBuilder('n')
        ->where('n.destinataire = :utilisateur')
        ->setParameter('utilisateur', $utilisateur)
        ->orderBy('n.dateEnvoi', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
  }

  /**
   * Marque toutes les notifications comme lues pour un utilisateur
   */
  public function markAllAsReadForUser(Utilisateur $utilisateur): int
  {
    return $this->createQueryBuilder('n')
        ->update()
        ->set('n.statut', ':statut')
        ->where('n.destinataire = :utilisateur')
        ->andWhere('n.statut = :oldStatut')
        ->setParameter('statut', 'lue')
        ->setParameter('oldStatut', 'non_lue')
        ->setParameter('utilisateur', $utilisateur)
        ->getQuery()
        ->execute();
  }

  /**
   * Supprime les anciennes notifications
   */
  public function deleteOldNotifications(int $days = 30): int
  {
    $date = new \DateTime();
    $date->modify("-{$days} days");

    return $this->createQueryBuilder('n')
        ->delete()
        ->where('n.dateEnvoi < :date')
        ->andWhere('n.statut = :statut')
        ->setParameter('date', $date)
        ->setParameter('statut', 'lue')
        ->getQuery()
        ->execute();
  }

  /**
   * Trouve les notifications par type
   */
  public function findByTypeForUser(Utilisateur $utilisateur, string $type, int $limit = 10): array
  {
    return $this->createQueryBuilder('n')
        ->where('n.destinataire = :utilisateur')
        ->andWhere('n.type = :type')
        ->setParameter('utilisateur', $utilisateur)
        ->setParameter('type', $type)
        ->orderBy('n.dateEnvoi', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
  }
}