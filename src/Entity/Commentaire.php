<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(type: 'text')]
  private ?string $contenu = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $dateCommentaire = null;

  #[ORM\Column(length: 50)]
  private ?string $etatValidation = 'en_attente';

// Dans l'entité Commentaire
  #[ORM\ManyToOne(inversedBy: 'commentaires')]
  #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
  private ?Signalement $signalement = null;

  #[ORM\ManyToOne(inversedBy: 'commentaires')]
  #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
  private ?Utilisateur $utilisateur = null;

  public function __construct()
  {
    $this->dateCommentaire = new \DateTime();
  }

  // Getters et setters...

  public function getId(): ?int
  {
      return $this->id;
  }

  public function getContenu(): ?string
  {
      return $this->contenu;
  }

  public function setContenu(string $contenu): static
  {
      $this->contenu = $contenu;

      return $this;
  }

  public function getDateCommentaire(): ?\DateTime
  {
      return $this->dateCommentaire;
  }

  public function setDateCommentaire(\DateTime $dateCommentaire): static
  {
      $this->dateCommentaire = $dateCommentaire;

      return $this;
  }

  public function getEtatValidation(): ?string
  {
      return $this->etatValidation;
  }

  public function setEtatValidation(string $etatValidation): static
  {
      $this->etatValidation = $etatValidation;

      return $this;
  }

  public function getUtilisateur(): ?Utilisateur
  {
      return $this->utilisateur;
  }

  public function setUtilisateur(?Utilisateur $utilisateur): static
  {
      $this->utilisateur = $utilisateur;

      return $this;
  }

  public function getSignalement(): ?Signalement
  {
      return $this->signalement;
  }

  public function setSignalement(?Signalement $signalement): static
  {
      $this->signalement = $signalement;

      return $this;
  }
}