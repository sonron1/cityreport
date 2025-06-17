<?php

namespace App\Entity;

use App\Repository\DepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: DepartementRepository::class)]
class Departement
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $nom = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $description = null;

  #[ORM\Column(length: 255, unique: true)]
  #[Gedmo\Slug(fields: ['nom'])]
  private ?string $slug = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $pays = 'Bénin';

  #[ORM\OneToMany(mappedBy: 'departement', targetEntity: Ville::class)]
  private Collection $villes;

  public function __construct()
  {
    $this->villes = new ArrayCollection();
  }

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

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): static
  {
    $this->description = $description;

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

  public function getPays(): ?string
  {
    return $this->pays;
  }

  public function setPays(?string $pays): static
  {
    $this->pays = $pays;

    return $this;
  }

  /**
   * @return Collection<int, Ville>
   */
  public function getVilles(): Collection
  {
    return $this->villes;
  }

  public function addVille(Ville $ville): static
  {
    if (!$this->villes->contains($ville)) {
      $this->villes->add($ville);
      $ville->setDepartement($this);
    }

    return $this;
  }

  public function removeVille(Ville $ville): static
  {
    if ($this->villes->removeElement($ville)) {
      // set the owning side to null (unless already changed)
      if ($ville->getDepartement() === $this) {
        $ville->setDepartement(null);
      }
    }

    return $this;
  }

  public function __toString(): string
  {
    return $this->nom ?? '';
  }
}