<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Ville;
use App\Entity\Utilisateur;
use App\Entity\Signalement;
use App\Entity\Cluster;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

/**
 * Tests complets pour l'entité Ville (après correction du constructeur)
 */
class VilleTestComplete extends TestCase
{
  private Ville $ville;

  protected function setUp(): void
  {
    $this->ville = new Ville();
  }

  public function testAllCollectionsInitialized(): void
  {
    // Vérifier que toutes les collections sont initialisées
    $this->assertInstanceOf(ArrayCollection::class, $this->ville->getArrondissements());
    $this->assertInstanceOf(ArrayCollection::class, $this->ville->getSignalements());
    $this->assertInstanceOf(ArrayCollection::class, $this->ville->getUtilisateurs());
    $this->assertInstanceOf(ArrayCollection::class, $this->ville->getClusters());
  }

  public function testUtilisateurCollection(): void
  {
    $utilisateur = $this->createMock(Utilisateur::class);

    // Test d'ajout
    $result = $this->ville->addUtilisateur($utilisateur);
    $this->assertInstanceOf(Ville::class, $result);
    $this->assertCount(1, $this->ville->getUtilisateurs());
    $this->assertTrue($this->ville->getUtilisateurs()->contains($utilisateur));

    // Test de suppression
    $this->ville->removeUtilisateur($utilisateur);
    $this->assertCount(0, $this->ville->getUtilisateurs());
    $this->assertFalse($this->ville->getUtilisateurs()->contains($utilisateur));
  }

  public function testSignalementCollection(): void
  {
    $signalement = $this->createMock(Signalement::class);

    // Test d'ajout
    $result = $this->ville->addSignalement($signalement);
    $this->assertInstanceOf(Ville::class, $result);
    $this->assertCount(1, $this->ville->getSignalements());
    $this->assertTrue($this->ville->getSignalements()->contains($signalement));

    // Test de suppression
    $this->ville->removeSignalement($signalement);
    $this->assertCount(0, $this->ville->getSignalements());
    $this->assertFalse($this->ville->getSignalements()->contains($signalement));
  }

  public function testClusterCollection(): void
  {
    $cluster = $this->createMock(Cluster::class);

    // Test d'ajout
    $result = $this->ville->addCluster($cluster);
    $this->assertInstanceOf(Ville::class, $result);
    $this->assertCount(1, $this->ville->getClusters());
    $this->assertTrue($this->ville->getClusters()->contains($cluster));

    // Test de suppression
    $this->ville->removeCluster($cluster);
    $this->assertCount(0, $this->ville->getClusters());
    $this->assertFalse($this->ville->getClusters()->contains($cluster));
  }

  public function testToString(): void
  {
    // Test avec nom défini
    $nom = 'Porto-Novo';
    $this->ville->setNom($nom);
    $this->assertEquals($nom, (string) $this->ville);

    // Test avec nom vide
    $villeVide = new Ville();
    $this->assertEquals('', (string) $villeVide);
  }
}