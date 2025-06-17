<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Message::class);
  }

  /**
   * Trouve les messages reçus par un utilisateur
   */
  public function findReceivedMessagesForUser(Utilisateur $user): array
  {
    return $this->createQueryBuilder('m')
        ->andWhere('m.destinataire = :user')
        ->andWhere('m.estArchive = false')
        ->setParameter('user', $user)
        ->orderBy('m.dateEnvoi', 'DESC')
        ->getQuery()
        ->getResult();
  }

  /**
   * Trouve les messages envoyés par un utilisateur
   */
  public function findSentMessagesByUser(Utilisateur $user): array
  {
    return $this->createQueryBuilder('m')
        ->andWhere('m.expediteur = :user')
        ->setParameter('user', $user)
        ->orderBy('m.dateEnvoi', 'DESC')
        ->getQuery()
        ->getResult();
  }

  /**
   * Compte les messages non lus pour un utilisateur
   */
  public function countUnreadMessagesForUser(Utilisateur $user): int
  {
    return $this->createQueryBuilder('m')
        ->select('COUNT(m.id)')
        ->andWhere('m.destinataire = :user')
        ->andWhere('m.estLu = false')
        ->andWhere('m.estArchive = false')
        ->setParameter('user', $user)
        ->getQuery()
        ->getSingleScalarResult();
  }
}