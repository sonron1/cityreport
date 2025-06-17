<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'message')]
class Message
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Assert\NotBlank(message: "Le sujet est obligatoire.")]
  private ?string $sujet = null;

  #[ORM\Column(type: Types::TEXT)]
  #[Assert\NotBlank(message: "Le contenu est obligatoire.")]
  private ?string $contenu = null;

  #[ORM\Column(name: 'date_envoi', type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $dateEnvoi = null;

  #[ORM\Column(name: 'est_lu')]
  private ?bool $estLu = false;

  #[ORM\Column(name: 'est_archive')]
  private ?bool $estArchive = false;

  // Relations
  #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'messagesEnvoyes')]
  #[ORM\JoinColumn(name: 'expediteur_id', nullable: false)]
  private ?Utilisateur $expediteur = null;

  #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'messagesRecus')]
  #[ORM\JoinColumn(name: 'destinataire_id', nullable: false)]
  private ?Utilisateur $destinataire = null;

  // ✅ CORRECTION : Ajout de inversedBy="messages"
  #[ORM\ManyToOne(targetEntity: Signalement::class, inversedBy: 'messages')]
  #[ORM\JoinColumn(name: 'signalement_concerne_id', nullable: true)]
  private ?Signalement $signalementConcerne = null;

  public function __construct()
  {
    $this->dateEnvoi = new \DateTime();
    $this->estLu = false;
    $this->estArchive = false;
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getSujet(): ?string
  {
    return $this->sujet;
  }

  public function setSujet(string $sujet): static
  {
    $this->sujet = $sujet;
    return $this;
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

  public function getDateEnvoi(): ?\DateTimeInterface
  {
    return $this->dateEnvoi;
  }

  public function setDateEnvoi(\DateTimeInterface $dateEnvoi): static
  {
    $this->dateEnvoi = $dateEnvoi;
    return $this;
  }

  public function isEstLu(): ?bool
  {
    return $this->estLu;
  }

  public function setEstLu(bool $estLu): static
  {
    $this->estLu = $estLu;
    return $this;
  }

  public function isEstArchive(): ?bool
  {
    return $this->estArchive;
  }

  public function setEstArchive(bool $estArchive): static
  {
    $this->estArchive = $estArchive;
    return $this;
  }

  public function getExpediteur(): ?Utilisateur
  {
    return $this->expediteur;
  }

  public function setExpediteur(?Utilisateur $expediteur): static
  {
    $this->expediteur = $expediteur;
    return $this;
  }

  public function getDestinataire(): ?Utilisateur
  {
    return $this->destinataire;
  }

  public function setDestinataire(?Utilisateur $destinataire): static
  {
    $this->destinataire = $destinataire;
    return $this;
  }

  public function getSignalementConcerne(): ?Signalement
  {
    return $this->signalementConcerne;
  }

  public function setSignalementConcerne(?Signalement $signalementConcerne): static
  {
    $this->signalementConcerne = $signalementConcerne;
    return $this;
  }

  public function __toString(): string
  {
    return sprintf(
        'Message #%d - %s (de %s à %s)',
        $this->id,
        $this->sujet,
        $this->expediteur?->getEmail() ?? 'Inconnu',
        $this->destinataire?->getEmail() ?? 'Inconnu'
    );
  }
}