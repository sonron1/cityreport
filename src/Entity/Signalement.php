<?php

namespace App\Entity;

use App\Enum\EtatValidation;
use App\Enum\StatutSignalement;
use App\Repository\SignalementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SignalementRepository::class)]
#[ORM\Table(name: 'signalement')]
class Signalement
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Assert\NotBlank(message: "Le titre ne peut pas être vide")]
  #[Assert\Length(
      min: 5,
      max: 255,
      minMessage: "Le titre doit contenir au moins {{ limit }} caractères",
      maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères"
  )]
  private ?string $titre = null;

  #[ORM\Column(type: Types::TEXT)]
  #[Assert\NotBlank(message: "La description ne peut pas être vide")]
  #[Assert\Length(
      min: 10,
      max: 5000,
      minMessage: "La description doit contenir au moins {{ limit }} caractères",
      maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
  )]
  private ?string $description = null;

  #[ORM\Column(name: 'photo_url', length: 255)]
  private ?string $photoUrl = null;

  #[ORM\Column(type: 'float')]
  #[Assert\NotBlank(message: "La latitude est obligatoire")]
  #[Assert\Range(
      min: -90,
      max: 90,
      notInRangeMessage: "La latitude doit être comprise entre {{ min }} et {{ max }}"
  )]
  private ?float $latitude = null;

  #[ORM\Column(type: 'float')]
  #[Assert\NotBlank(message: "La longitude est obligatoire")]
  #[Assert\Range(
      min: -180,
      max: 180,
      notInRangeMessage: "La longitude doit être comprise entre {{ min }} et {{ max }}"
  )]
  private ?float $longitude = null;

  #[ORM\Column(name: 'date_signalement', type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $dateSignalement = null;

  #[ORM\Column(length: 50, enumType: StatutSignalement::class)]
  private ?StatutSignalement $statut = StatutSignalement::NOUVEAU;

  #[ORM\Column]
  #[Assert\Range(
      min: 1,
      max: 5,
      notInRangeMessage: "La priorité doit être comprise entre {{ min }} et {{ max }}"
  )]
  private ?int $priorite = 1;

  #[ORM\Column(enumType: EtatValidation::class)]
  private EtatValidation $etatValidation = EtatValidation::EN_ATTENTE; // ✅ Valeur par défaut

  #[ORM\Column(name: 'demande_suppression_statut', length: 20, nullable: true)]
  private ?string $demandeSuppressionStatut = null;

  #[ORM\Column(name: 'date_demande_suppression_statut', type: Types::DATETIME_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $dateDemandeSuppressionStatut = null;

  // Ajoutez ces propriétés dans votre entité Signalement après les autres propriétés de demande de suppression

  #[ORM\Column(name: 'commentaire_suppression_statut', type: Types::TEXT, nullable: true)]
  private ?string $commentaireSuppressionStatut = null;

  #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
  #[ORM\JoinColumn(name: 'moderateur_suppression_statut_id', nullable: true)]
  private ?Utilisateur $moderateurSuppressionStatut = null;

// Ajoutez ces méthodes getter/setter

  public function getCommentaireSuppressionStatut(): ?string
  {
    return $this->commentaireSuppressionStatut;
  }

  public function setCommentaireSuppressionStatut(?string $commentaire): static
  {
    $this->commentaireSuppressionStatut = $commentaire;
    return $this;
  }

  public function getModerateurSuppressionStatut(): ?Utilisateur
  {
    return $this->moderateurSuppressionStatut;
  }

  public function setModerateurSuppressionStatut(?Utilisateur $moderateur): static
  {
    $this->moderateurSuppressionStatut = $moderateur;
    return $this;
  }

  // Relations
  #[ORM\ManyToOne(inversedBy: 'signalements')]
  #[ORM\JoinColumn(name: 'utilisateur_id', nullable: false)]
  private ?Utilisateur $utilisateur = null;

  #[ORM\ManyToOne(inversedBy: 'signalements')]
  #[ORM\JoinColumn(name: 'categorie_id', nullable: false)]
  private ?Categorie $categorie = null;

  #[ORM\ManyToOne(inversedBy: 'signalements')]
  #[ORM\JoinColumn(name: 'ville_id', nullable: false)]
  private ?Ville $ville = null;

  #[ORM\ManyToOne(inversedBy: 'signalements')]
  #[ORM\JoinColumn(name: 'arrondissement_id', nullable: true)]
  private ?Arrondissement $arrondissement = null;

  #[ORM\ManyToOne(inversedBy: 'signalements')]
  #[ORM\JoinColumn(name: 'cluster_id', nullable: true)]
  private ?Cluster $cluster = null;

  #[ORM\OneToMany(mappedBy: 'signalement', targetEntity: Commentaire::class, cascade: ['remove'])]
  private Collection $commentaires;

  #[ORM\OneToMany(mappedBy: 'signalement', targetEntity: JournalValidation::class, cascade: ['remove'])]
  private Collection $journalValidations;

  #[ORM\OneToMany(mappedBy: 'signalement', targetEntity: Notification::class, cascade: ['remove'])]
  private Collection $notifications;

  #[ORM\OneToMany(mappedBy: 'signalementConcerne', targetEntity: Message::class)]
  private Collection $messages;

  #[ORM\OneToOne(mappedBy: 'signalement', cascade: ['persist', 'remove'])]
  private ?Reparation $reparation = null;

  public function __construct()
  {
    $this->commentaires = new ArrayCollection();
    $this->journalValidations = new ArrayCollection();
    $this->notifications = new ArrayCollection();
    $this->messages = new ArrayCollection();
    $this->etatValidation = EtatValidation::EN_ATTENTE;
    $this->dateSignalement = new \DateTime();
    $this->statut = StatutSignalement::NOUVEAU;
    $this->priorite = 1;
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getTitre(): ?string
  {
    return $this->titre;
  }

  public function setTitre(string $titre): static
  {
    $this->titre = $titre;
    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(string $description): static
  {
    $this->description = $description;
    return $this;
  }

  public function getPhotoUrl(): ?string
  {
    return $this->photoUrl;
  }

  public function setPhotoUrl(?string $photoUrl): static
  {
    $this->photoUrl = $photoUrl;
    return $this;
  }

  public function getLatitude(): ?float
  {
    return $this->latitude;
  }

  public function setLatitude(float $latitude): static
  {
    $this->latitude = $latitude;
    return $this;
  }

  public function getLongitude(): ?float
  {
    return $this->longitude;
  }

  public function setLongitude(float $longitude): static
  {
    $this->longitude = $longitude;
    return $this;
  }

  public function getDateSignalement(): ?\DateTimeInterface
  {
    return $this->dateSignalement;
  }

  public function setDateSignalement(\DateTimeInterface $dateSignalement): static
  {
    $this->dateSignalement = $dateSignalement;
    return $this;
  }

  public function getStatut(): ?StatutSignalement
  {
    return $this->statut;
  }

  public function setStatut(StatutSignalement $statut): static
  {
    $this->statut = $statut;
    return $this;
  }

  public function getPriorite(): ?int
  {
    return $this->priorite;
  }

  public function setPriorite(int $priorite): static
  {
    $this->priorite = $priorite;
    return $this;
  }

  public function getEtatValidation(): ?EtatValidation
  {
    return $this->etatValidation;
  }

  public function setEtatValidation(EtatValidation $etatValidation): static
  {
    $this->etatValidation = $etatValidation;
    return $this;
  }

  public function getDemandeSuppressionStatut(): ?string
  {
    return $this->demandeSuppressionStatut;
  }

  public function setDemandeSuppressionStatut(?string $statut): static
  {
    $this->demandeSuppressionStatut = $statut;
    if ($statut) {
      $this->dateDemandeSuppressionStatut = new \DateTime();
    }
    return $this;
  }

  public function getDateDemandeSuppressionStatut(): ?\DateTimeInterface
  {
    return $this->dateDemandeSuppressionStatut;
  }

  public function setDateDemandeSuppressionStatut(?\DateTimeInterface $date): static
  {
    $this->dateDemandeSuppressionStatut = $date;
    return $this;
  }

  // Relations getters/setters
  public function getUtilisateur(): ?Utilisateur
  {
    return $this->utilisateur;
  }

  public function setUtilisateur(?Utilisateur $utilisateur): static
  {
    $this->utilisateur = $utilisateur;
    return $this;
  }

  public function getCategorie(): ?Categorie
  {
    return $this->categorie;
  }

  public function setCategorie(?Categorie $categorie): static
  {
    $this->categorie = $categorie;
    return $this;
  }

  public function getVille(): ?Ville
  {
    return $this->ville;
  }

  public function setVille(?Ville $ville): static
  {
    $this->ville = $ville;
    return $this;
  }

  public function getArrondissement(): ?Arrondissement
  {
    return $this->arrondissement;
  }

  public function setArrondissement(?Arrondissement $arrondissement): static
  {
    $this->arrondissement = $arrondissement;
    return $this;
  }

  public function getCluster(): ?Cluster
  {
    return $this->cluster;
  }

  public function setCluster(?Cluster $cluster): static
  {
    $this->cluster = $cluster;
    return $this;
  }

  /**
   * @return Collection<int, Commentaire>
   */
  public function getCommentaires(): Collection
  {
    return $this->commentaires;
  }

  public function addCommentaire(Commentaire $commentaire): static
  {
    if (!$this->commentaires->contains($commentaire)) {
      $this->commentaires->add($commentaire);
      $commentaire->setSignalement($this);
    }
    return $this;
  }

  public function removeCommentaire(Commentaire $commentaire): static
  {
    if ($this->commentaires->removeElement($commentaire)) {
      if ($commentaire->getSignalement() === $this) {
        $commentaire->setSignalement(null);
      }
    }
    return $this;
  }

  /**
   * @return Collection<int, JournalValidation>
   */
  public function getJournalValidations(): Collection
  {
    return $this->journalValidations;
  }

  public function addJournalValidation(JournalValidation $journalValidation): static
  {
    if (!$this->journalValidations->contains($journalValidation)) {
      $this->journalValidations->add($journalValidation);
      $journalValidation->setSignalement($this);
    }
    return $this;
  }

  public function removeJournalValidation(JournalValidation $journalValidation): static
  {
    if ($this->journalValidations->removeElement($journalValidation)) {
      if ($journalValidation->getSignalement() === $this) {
        $journalValidation->setSignalement(null);
      }
    }
    return $this;
  }

  /**
   * @return Collection<int, Notification>
   */
  public function getNotifications(): Collection
  {
    return $this->notifications;
  }

  public function addNotification(Notification $notification): static
  {
    if (!$this->notifications->contains($notification)) {
      $this->notifications->add($notification);
      $notification->setSignalement($this);
    }
    return $this;
  }

  public function removeNotification(Notification $notification): static
  {
    if ($this->notifications->removeElement($notification)) {
      if ($notification->getSignalement() === $this) {
        $notification->setSignalement(null);
      }
    }
    return $this;
  }

  /**
   * @return Collection<int, Message>
   */
  public function getMessages(): Collection
  {
    return $this->messages;
  }

  public function addMessage(Message $message): static
  {
    if (!$this->messages->contains($message)) {
      $this->messages->add($message);
      $message->setSignalementConcerne($this);
    }
    return $this;
  }

  public function removeMessage(Message $message): static
  {
    if ($this->messages->removeElement($message)) {
      if ($message->getSignalementConcerne() === $this) {
        $message->setSignalementConcerne(null);
      }
    }
    return $this;
  }

  public function getReparation(): ?Reparation
  {
    return $this->reparation;
  }

  public function setReparation(?Reparation $reparation): static
  {
    if ($reparation === null && $this->reparation !== null) {
      $this->reparation->setSignalement(null);
    }

    if ($reparation !== null && $reparation->getSignalement() !== $this) {
      $reparation->setSignalement($this);
    }

    $this->reparation = $reparation;
    return $this;
  }

  // Méthodes utilitaires
  public function getCoordonnees(): array
  {
    return [
        'latitude' => $this->latitude,
        'longitude' => $this->longitude
    ];
  }

  public function setCoordonnees(float $latitude, float $longitude): static
  {
    $this->latitude = $latitude;
    $this->longitude = $longitude;
    return $this;
  }

  public function isValide(): bool
  {
    return $this->etatValidation === EtatValidation::VALIDE;
  }

  public function isRejete(): bool
  {
    return $this->etatValidation === EtatValidation::REJETE;
  }

  public function isEnAttente(): bool
  {
    return $this->etatValidation === EtatValidation::EN_ATTENTE;
  }

  public function __toString(): string
  {
    return sprintf(
        'Signalement #%d - %s (%s)',
        $this->id,
        $this->titre,
        $this->statut?->value ?? 'Statut inconnu'
    );
  }
}