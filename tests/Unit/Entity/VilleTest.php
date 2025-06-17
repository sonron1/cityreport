<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Ville;
use App\Entity\Arrondissement;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class VilleTest extends TestCase
{
  private Ville $ville;

  protected function setUp(): void
  {
    $this->ville = new Ville();
  }

  public function testVilleCreation(): void
  {
    $this->assertInstanceOf(Ville::class, $this->ville);

    // Vérifier que les collections initialisées sont bien là
    $this->assertInstanceOf(ArrayCollection::class, $this->ville->getArrondissements());
    $this->assertInstanceOf(ArrayCollection::class, $this->ville->getSignalements());

    // Vérifier que les collections sont vides
    $this->assertCount(0, $this->ville->getArrondissements());
    $this->assertCount(0, $this->ville->getSignalements());
  }

  public function testSetNom(): void
  {
    $nom = 'Cotonou';
    $this->ville->setNom($nom);

    $this->assertEquals($nom, $this->ville->getNom());
  }

  public function testSetSlug(): void
  {
    $slug = 'cotonou-ville';
    $this->ville->setSlug($slug);

    $this->assertEquals($slug, $this->ville->getSlug());
  }

  public function testSetCoordinatesCentre(): void
  {
    $latitude = 6.3703;
    $longitude = 2.3912;

    $this->ville->setLatitudeCentre($latitude);
    $this->ville->setLongitudeCentre($longitude);

    $this->assertEquals($latitude, $this->ville->getLatitudeCentre());
    $this->assertEquals($longitude, $this->ville->getLongitudeCentre());
  }

  public function testArrondissementCollection(): void
  {
    $arrondissement = $this->createMock(Arrondissement::class);

    // Test d'ajout
    $this->ville->addArrondissement($arrondissement);
    $this->assertCount(1, $this->ville->getArrondissements());
    $this->assertTrue($this->ville->getArrondissements()->contains($arrondissement));

    // Test de suppression
    $this->ville->removeArrondissement($arrondissement);
    $this->assertCount(0, $this->ville->getArrondissements());
    $this->assertFalse($this->ville->getArrondissements()->contains($arrondissement));
  }

  public function testGettersReturnCorrectTypes(): void
  {
    $this->assertNull($this->ville->getId());
    $this->assertNull($this->ville->getNom());
    $this->assertNull($this->ville->getSlug());
    $this->assertNull($this->ville->getLatitudeCentre());
    $this->assertNull($this->ville->getLongitudeCentre());
    $this->assertNull($this->ville->getDepartement());
  }

  public function testFluentInterface(): void
  {
    $result = $this->ville
        ->setNom('Porto-Novo')
        ->setSlug('porto-novo')
        ->setLatitudeCentre(6.4969)
        ->setLongitudeCentre(2.6289);

    $this->assertInstanceOf(Ville::class, $result);
    $this->assertEquals('Porto-Novo', $this->ville->getNom());
    $this->assertEquals('porto-novo', $this->ville->getSlug());
    $this->assertEquals(6.4969, $this->ville->getLatitudeCentre());
    $this->assertEquals(2.6289, $this->ville->getLongitudeCentre());
  }

  public function testSettersReturnSelf(): void
  {
    // Test que tous les setters retournent bien l'instance
    $this->assertSame($this->ville, $this->ville->setNom('Test'));
    $this->assertSame($this->ville, $this->ville->setSlug('test'));
    $this->assertSame($this->ville, $this->ville->setLatitudeCentre(1.23));
    $this->assertSame($this->ville, $this->ville->setLongitudeCentre(4.56));
  }

  public function testBasicProperties(): void
  {
    // Test des propriétés de base
    $this->ville->setNom('Cotonou');
    $this->ville->setSlug('cotonou');
    $this->ville->setLatitudeCentre(6.3703);
    $this->ville->setLongitudeCentre(2.3912);

    $this->assertEquals('Cotonou', $this->ville->getNom());
    $this->assertEquals('cotonou', $this->ville->getSlug());
    $this->assertEquals(6.3703, $this->ville->getLatitudeCentre());
    $this->assertEquals(2.3912, $this->ville->getLongitudeCentre());
  }
}