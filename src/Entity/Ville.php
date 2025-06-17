<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255, unique: true)]
  private ?string $nom = null;

  #[ORM\Column(length: 255, unique: true)]
  #[Gedmo\Slug(fields: ['nom'])]
  private ?string $slug = null;

  #[ORM\Column]
  private ?float $latitudeCentre = null;

  #[ORM\Column]
  private ?float $longitudeCentre = null;

  #[ORM\OneToMany(mappedBy: 'villeResidence', targetEntity: Utilisateur::class)]
  private Collection $utilisateurs;

  #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Signalement::class)]
  private Collection $signalements;

  #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Cluster::class)]
  private Collection $clusters;

  #[ORM\ManyToOne(inversedBy: 'villes')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Departement $departement = null;

  #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Arrondissement::class)]
  private Collection $arrondissements;

  public function __construct()
  {
    $this->arrondissements = new ArrayCollection();
    $this->signalements = new ArrayCollection();
    $this->utilisateurs = new ArrayCollection();  // ✅ Ajoutez cette ligne
    $this->clusters = new ArrayCollection();      // ✅ Ajoutez cette ligne
  }

  // Getters et setters...

  public function getId(): ?int
  {
      return $this->id;
  }

  public function getNom(): ?string
  {
      return $this->nom;
  }

  public function setNom(string $nom): static
  {
      $this->nom = $nom;

      return $this;
  }

  public function getSlug(): ?string
  {
      return $this->slug;
  }

  public function setSlug(string $slug): static
  {
      $this->slug = $slug;

      return $this;
  }

  public function getLatitudeCentre(): ?float
  {
      return $this->latitudeCentre;
  }

  public function setLatitudeCentre(float $latitudeCentre): static
  {
      $this->latitudeCentre = $latitudeCentre;

      return $this;
  }

  public function getLongitudeCentre(): ?float
  {
      return $this->longitudeCentre;
  }

  public function setLongitudeCentre(float $longitudeCentre): static
  {
      $this->longitudeCentre = $longitudeCentre;

      return $this;
  }

  /**
   * @return Collection<int, Utilisateur>
   */
  public function getUtilisateurs(): Collection
  {
      return $this->utilisateurs;
  }

  public function addUtilisateur(Utilisateur $utilisateur): static
  {
      if (!$this->utilisateurs->contains($utilisateur)) {
          $this->utilisateurs->add($utilisateur);
          $utilisateur->setVilleResidence($this);
      }

      return $this;
  }

  public function removeUtilisateur(Utilisateur $utilisateur): static
  {
      if ($this->utilisateurs->removeElement($utilisateur)) {
          // set the owning side to null (unless already changed)
          if ($utilisateur->getVilleResidence() === $this) {
              $utilisateur->setVilleResidence(null);
          }
      }

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
          $signalement->setVille($this);
      }

      return $this;
  }

  public function removeSignalement(Signalement $signalement): static
  {
      if ($this->signalements->removeElement($signalement)) {
          // set the owning side to null (unless already changed)
          if ($signalement->getVille() === $this) {
              $signalement->setVille(null);
          }
      }

      return $this;
  }



  public function getDepartement(): ?Departement
  {
    return $this->departement;
  }

  public function setDepartement(?Departement $departement): static
  {
    $this->departement = $departement;

    return $this;
  }

  /**
   * @return Collection<int, Arrondissement>
   */
  public function getArrondissements(): Collection
  {
    return $this->arrondissements;
  }

  public function addArrondissement(Arrondissement $arrondissement): static
  {
    if (!$this->arrondissements->contains($arrondissement)) {
      $this->arrondissements->add($arrondissement);
      $arrondissement->setVille($this);
    }

    return $this;
  }

  public function removeArrondissement(Arrondissement $arrondissement): static
  {
    if ($this->arrondissements->removeElement($arrondissement)) {
      // set the owning side to null (unless already changed)
      if ($arrondissement->getVille() === $this) {
        $arrondissement->setVille(null);
      }
    }

    return $this;
  }


  /**
   * @return Collection<int, Cluster>
   */
  public function getClusters(): Collection
  {
      return $this->clusters;
  }

  public function addCluster(Cluster $cluster): static
  {
      if (!$this->clusters->contains($cluster)) {
          $this->clusters->add($cluster);
          $cluster->setVille($this);
      }

      return $this;
  }

  public function removeCluster(Cluster $cluster): static
  {
      if ($this->clusters->removeElement($cluster)) {
          // set the owning side to null (unless already changed)
          if ($cluster->getVille() === $this) {
              $cluster->setVille(null);
          }
      }

      return $this;
  }
  // Ajoutez également la méthode __toString() à la fin de la classe
  public function __toString(): string
  {
    return $this->nom ?? '';
  }
}