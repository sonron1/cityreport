<?php

namespace App\Entity;

use App\Repository\ClusterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClusterRepository::class)]
#[ORM\Table(name: 'cluster')]
class Cluster
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  // ✅ CORRECTION : double precision comme en BD
  #[ORM\Column(type: 'float')]
  private ?float $latitude = null;

  #[ORM\Column(type: 'float')]
  private ?float $longitude = null;

  #[ORM\Column(type: 'float')]
  private ?float $rayon = null;

  #[ORM\Column(name: 'nombre_signalements')]
  private ?int $nombreSignalements = null;

  // Relations
  #[ORM\ManyToOne(inversedBy: 'clusters')]
  #[ORM\JoinColumn(name: 'ville_id', nullable: false)]
  private ?Ville $ville = null;

  #[ORM\OneToMany(mappedBy: 'cluster', targetEntity: Signalement::class)]
  private Collection $signalements;

  public function __construct()
  {
    $this->signalements = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
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

  public function getRayon(): ?float
  {
    return $this->rayon;
  }

  public function setRayon(float $rayon): static
  {
    $this->rayon = $rayon;
    return $this;
  }

  public function getNombreSignalements(): ?int
  {
    return $this->nombreSignalements;
  }

  public function setNombreSignalements(int $nombreSignalements): static
  {
    $this->nombreSignalements = $nombreSignalements;
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
      $signalement->setCluster($this);
    }
    return $this;
  }

  public function removeSignalement(Signalement $signalement): static
  {
    if ($this->signalements->removeElement($signalement)) {
      if ($signalement->getCluster() === $this) {
        $signalement->setCluster(null);
      }
    }
    return $this;
  }
}