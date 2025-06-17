<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: "L'adresse email '{{ value }}' n'est pas valide.")]
    #[Assert\NotBlank(message: "L'adresse email est obligatoire.")]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    private ?string $prenom = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $villeResidence = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateInscription = null;

    #[ORM\Column]
    private ?bool $estValide = false;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Signalement::class)]
    private Collection $signalements;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'moderateur', targetEntity: JournalValidation::class)]
    private Collection $journalValidations;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Reparation::class)]
    private Collection $reparations;

    #[ORM\OneToMany(mappedBy: 'destinataire', targetEntity: Notification::class)]
    private Collection $notifications;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $confirmationToken = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $tokenExpiryDate = null;

    #[ORM\OneToMany(mappedBy: 'expediteur', targetEntity: Message::class)]
    private Collection $messagesEnvoyes;

    #[ORM\OneToMany(mappedBy: 'destinataire', targetEntity: Message::class)]
    private Collection $messagesRecus;

    public function __construct()
    {
        $this->signalements = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->journalValidations = new ArrayCollection();
        $this->reparations = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->dateInscription = new \DateTime();
        $this->roles = ['ROLE_USER'];
          // ... autres initialisations
    $this->messagesEnvoyes = new ArrayCollection();
    $this->messagesRecus = new ArrayCollection();
    }

    // Méthodes existantes...
    
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }

    public function getTokenExpiryDate(): ?\DateTimeInterface
    {
        return $this->tokenExpiryDate;
    }

    public function setTokenExpiryDate(?\DateTimeInterface $tokenExpiryDate): self
    {
        $this->tokenExpiryDate = $tokenExpiryDate;
        return $this;
    }

    public function isTokenExpired(): bool
    {
        if (!$this->tokenExpiryDate) {
            return true;
        }
        
        return $this->tokenExpiryDate < new \DateTime();
    }
    
    // Reste du code...
  // Getters et setters...
  public function getPassword(): ?string
  {
    return $this->password;
  }

  public function getRoles(): array
  {
    return $this->roles;
  }

  public function eraseCredentials(): void
  {
    // TODO: Implement eraseCredentials() method.
  }

  public function getUserIdentifier(): string
  {
    return $this->email;
  }
public function getNom(): ?string
{
    return $this->nom;
}
public function setNom(string $nom): self
{
    $this->nom = $nom;
    
    return $this;
}

public function getId(): ?int
{
    return $this->id;
}

public function getEmail(): ?string
{
    return $this->email;
}

public function setEmail(string $email): static
{
    $this->email = $email;

    return $this;
}

public function setRoles(array $roles): static
{
    $this->roles = $roles;

    return $this;
}

public function setPassword(string $password): static
{
    $this->password = $password;

    return $this;
}

public function getPrenom(): ?string
{
    return $this->prenom;
}

public function setPrenom(string $prenom): static
{
    $this->prenom = $prenom;

    return $this;
}

public function getDateInscription(): ?\DateTime
{
    return $this->dateInscription;
}

public function setDateInscription(\DateTime $dateInscription): static
{
    $this->dateInscription = $dateInscription;

    return $this;
}

public function isEstValide(): ?bool
{
    return $this->estValide;
}

public function setEstValide(bool $estValide): static
{
    $this->estValide = $estValide;

    return $this;
}

public function getVilleResidence(): ?Ville
{
    return $this->villeResidence;
}

public function setVilleResidence(?Ville $villeResidence): static
{
    $this->villeResidence = $villeResidence;

    return $this;
}

/**
 * @return Collection<int, Signalement>
 */
public function getSignalements(): Collection
{
    return $this->signalements;
}

public function addSignalement(Signalement $signalement): static
{
    if (!$this->signalements->contains($signalement)) {
        $this->signalements->add($signalement);
        $signalement->setUtilisateur($this);
    }

    return $this;
}

public function removeSignalement(Signalement $signalement): static
{
    if ($this->signalements->removeElement($signalement)) {
        // set the owning side to null (unless already changed)
        if ($signalement->getUtilisateur() === $this) {
            $signalement->setUtilisateur(null);
        }
    }

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
        $commentaire->setUtilisateur($this);
    }

    return $this;
}

public function removeCommentaire(Commentaire $commentaire): static
{
    if ($this->commentaires->removeElement($commentaire)) {
        // set the owning side to null (unless already changed)
        if ($commentaire->getUtilisateur() === $this) {
            $commentaire->setUtilisateur(null);
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
        $journalValidation->setModerateur($this);
    }

    return $this;
}

public function removeJournalValidation(JournalValidation $journalValidation): static
{
    if ($this->journalValidations->removeElement($journalValidation)) {
        // set the owning side to null (unless already changed)
        if ($journalValidation->getModerateur() === $this) {
            $journalValidation->setModerateur(null);
        }
    }

    return $this;
}

/**
 * @return Collection<int, Reparation>
 */
public function getReparations(): Collection
{
    return $this->reparations;
}

public function addReparation(Reparation $reparation): static
{
    if (!$this->reparations->contains($reparation)) {
        $this->reparations->add($reparation);
        $reparation->setUtilisateur($this);
    }

    return $this;
}

public function removeReparation(Reparation $reparation): static
{
    if ($this->reparations->removeElement($reparation)) {
        // set the owning side to null (unless already changed)
        if ($reparation->getUtilisateur() === $this) {
            $reparation->setUtilisateur(null);
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
        $notification->setDestinataire($this);
    }

    return $this;
}

public function removeNotification(Notification $notification): static
{
    if ($this->notifications->removeElement($notification)) {
        // set the owning side to null (unless already changed)
        if ($notification->getDestinataire() === $this) {
            $notification->setDestinataire(null);
        }
    }

    return $this;
}

/**
 * @return Collection<int, Message>
 */
public function getMessagesEnvoyes(): Collection
{
    return $this->messagesEnvoyes;
}

public function addMessageEnvoye(Message $message): static
{
    if (!$this->messagesEnvoyes->contains($message)) {
        $this->messagesEnvoyes->add($message);
        $message->setExpediteur($this);
    }
    return $this;
}

public function removeMessageEnvoye(Message $message): static
{
    if ($this->messagesEnvoyes->removeElement($message)) {
        if ($message->getExpediteur() === $this) {
            $message->setExpediteur(null);
        }
    }
    return $this;
}

/**
 * @return Collection<int, Message>
 */
public function getMessagesRecus(): Collection
{
    return $this->messagesRecus;
}

public function addMessageRecu(Message $message): static
{
    if (!$this->messagesRecus->contains($message)) {
        $this->messagesRecus->add($message);
        $message->setDestinataire($this);
    }
    return $this;
}

public function removeMessageRecu(Message $message): static
{
    if ($this->messagesRecus->removeElement($message)) {
        if ($message->getDestinataire() === $this) {
            $message->setDestinataire(null);
        }
    }
    return $this;
}
}