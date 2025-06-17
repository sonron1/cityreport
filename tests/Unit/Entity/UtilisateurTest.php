<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Utilisateur;
use App\Entity\Ville;
use PHPUnit\Framework\TestCase;

class UtilisateurTest extends TestCase
{
  private Utilisateur $utilisateur;
  private Ville $ville;

  protected function setUp(): void
  {
    $this->ville = new Ville();
    $this->ville->setNom('Cotonou');

    $this->utilisateur = new Utilisateur();
  }

  public function testUtilisateurCreation(): void
  {
    $this->assertInstanceOf(Utilisateur::class, $this->utilisateur);
    $this->assertFalse($this->utilisateur->isEstValide());
    $this->assertEquals(['ROLE_USER'], $this->utilisateur->getRoles());
    $this->assertInstanceOf(\DateTime::class, $this->utilisateur->getDateInscription());
  }

  public function testSetEmail(): void
  {
    $email = 'test@example.com';
    $this->utilisateur->setEmail($email);

    $this->assertEquals($email, $this->utilisateur->getEmail());
    $this->assertEquals($email, $this->utilisateur->getUserIdentifier());
  }

  public function testSetNomAndPrenom(): void
  {
    $nom = 'Doe';
    $prenom = 'John';

    $this->utilisateur->setNom($nom);
    $this->utilisateur->setPrenom($prenom);

    $this->assertEquals($nom, $this->utilisateur->getNom());
    $this->assertEquals($prenom, $this->utilisateur->getPrenom());
  }

  public function testSetPassword(): void
  {
    $password = 'hashed_password_123';
    $this->utilisateur->setPassword($password);

    $this->assertEquals($password, $this->utilisateur->getPassword());
  }

  public function testSetVilleResidence(): void
  {
    $this->utilisateur->setVilleResidence($this->ville);

    $this->assertEquals($this->ville, $this->utilisateur->getVilleResidence());
  }

  public function testSetEstValide(): void
  {
    $this->assertFalse($this->utilisateur->isEstValide());

    $this->utilisateur->setEstValide(true);
    $this->assertTrue($this->utilisateur->isEstValide());
  }

  public function testRoles(): void
  {
    // Test rôle par défaut
    $this->assertEquals(['ROLE_USER'], $this->utilisateur->getRoles());

    // Test ajout de rôles
    $roles = ['ROLE_USER', 'ROLE_ADMIN'];
    $this->utilisateur->setRoles($roles);
    $this->assertEquals($roles, $this->utilisateur->getRoles());
  }
}