<?php

namespace App\Entity;

use App\Enum\StatutReparation;
use App\Repository\ReparationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReparationRepository::class)]
#[ORM\Table(name: 'reparation')]
class Reparation
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(name: 'date_debut', type: Types::DATETIME_MUTABLE)]
  #[Assert\NotBlank(message: 'La date de début est obligatoire')]
  private ?\DateTimeInterface $dateDebut = null;

  #[ORM\Column(name: 'date_fin', type: Types::DATETIME_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $dateFin = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  #[Assert\Length(
      max: 1000,
      maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères'
  )]
  private ?string $description = null;

  #[ORM\Column(length: 50, enumType: StatutReparation::class)]
  private ?StatutReparation $statut = StatutReparation::PLANIFIEE;

  // ✅ CORRECTION : numeric(10,2) comme en BD
  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
  #[Assert\PositiveOrZero(message: 'Le coût ne peut pas être négatif')]
  private ?string $cout = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  #[Assert\Length(
      max: 2000,
      maxMessage: 'Le commentaire ne peut pas dépasser {{ limit }} caractères'
  )]
  private ?string $commentaire = null;

  // Relations
  #[ORM\OneToOne(inversedBy: 'reparation')]
  #[ORM\JoinColumn(name: 'signalement_id', nullable: false)]
  private ?Signalement $signalement = null;

  #[ORM\ManyToOne(inversedBy: 'reparations')]
  #[ORM\JoinColumn(name: 'utilisateur_id', nullable: true)]
  private ?Utilisateur $utilisateur = null;

  public function __construct()
  {
    $this->statut = StatutReparation::PLANIFIEE;
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getDateDebut(): ?\DateTimeInterface
  {
    return $this->dateDebut;
  }

  public function setDateDebut(\DateTimeInterface $dateDebut): static
  {
    $this->dateDebut = $dateDebut;
    return $this;
  }

  public function getDateFin(): ?\DateTimeInterface
  {
    return $this->dateFin;
  }

  public function setDateFin(?\DateTimeInterface $dateFin): static
  {
    $this->dateFin = $dateFin;
    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): static
  {
    $this->description = $description;
    return $this;
  }

  public function getStatut(): ?StatutReparation
  {
    return $this->statut;
  }

  public function setStatut(StatutReparation $statut): static
  {
    $this->statut = $statut;
    return $this;
  }

  public function getCout(): ?string
  {
    return $this->cout;
  }

  public function setCout(string|float|null $cout): static
  {
    $this->cout = $cout !== null ? (string) $cout : null;
    return $this;
  }

  // Méthode utilitaire pour récupérer le coût en float
  public function getCoutFloat(): ?float
  {
    return $this->cout ? (float) $this->cout : null;
  }

  public function getCommentaire(): ?string
  {
    return $this->commentaire;
  }

  public function setCommentaire(?string $commentaire): static
  {
    $this->commentaire = $commentaire;
    return $this;
  }

  public function getSignalement(): ?Signalement
  {
    return $this->signalement;
  }

  public function setSignalement(Signalement $signalement): static
  {
    $this->signalement = $signalement;
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

  // Méthodes utilitaires
  public function getCoutFormatte(): string
  {
    if ($this->cout === null) {
      return 'Non défini';
    }

    $cout = (float) $this->cout;
    return number_format($cout, 2, ',', ' ') . ' €';
  }

  public function isEnCours(): bool
  {
    return $this->statut === StatutReparation::EN_COURS;
  }

  public function isTerminee(): bool
  {
    return $this->statut === StatutReparation::TERMINEE;
  }

  public function __toString(): string
  {
    return sprintf(
        'Réparation #%d - %s (%s)',
        $this->id,
        $this->signalement?->getTitre() ?? 'Sans titre',
        $this->statut?->value ?? 'Statut inconnu'
    );
  }
}