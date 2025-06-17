<?php

namespace App\Entity;

use App\Repository\JournalValidationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JournalValidationRepository::class)]
class JournalValidation
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(inversedBy: 'journalValidations')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Signalement $signalement = null;

  #[ORM\ManyToOne(inversedBy: 'journalValidations')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Utilisateur $moderateur = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $dateValidation = null;

  #[ORM\Column(length: 50)]
  private ?string $action = null;

  #[ORM\Column(type: 'text', nullable: true)]
  private ?string $commentaire = null;

  public function __construct()
  {
    $this->dateValidation = new \DateTime();
  }

  // Getters et setters...

  public function getId(): ?int
  {
      return $this->id;
  }

  public function getDateValidation(): ?\DateTime
  {
      return $this->dateValidation;
  }

  public function setDateValidation(\DateTime $dateValidation): static
  {
      $this->dateValidation = $dateValidation;

      return $this;
  }

  public function getAction(): ?string
  {
      return $this->action;
  }

  public function setAction(string $action): static
  {
      $this->action = $action;

      return $this;
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

  public function setSignalement(?Signalement $signalement): static
  {
      $this->signalement = $signalement;

      return $this;
  }

  public function getModerateur(): ?Utilisateur
  {
      return $this->moderateur;
  }

  public function setModerateur(?Utilisateur $moderateur): static
  {
      $this->moderateur = $moderateur;

      return $this;
  }
}