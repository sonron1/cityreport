<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class UtilisateurRepositoryTest extends KernelTestCase
{
  private EntityManagerInterface $entityManager;
  private UtilisateurRepository $repository;

  protected function setUp(): void
  {
    $kernel = self::bootKernel();
    $this->entityManager = $kernel->getContainer()
        ->get('doctrine')
        ->getManager();

    $this->repository = $this->entityManager
        ->getRepository(Utilisateur::class);
  }

  public function testFindByEmail(): void
  {
    // Créer une ville de test
    $ville = new Ville();
    $ville->setNom('Test Ville');
    $this->entityManager->persist($ville);

    // Créer un utilisateur de test
    $utilisateur = new Utilisateur();
    $utilisateur->setEmail('test@example.com');
    $utilisateur->setNom('Test');
    $utilisateur->setPrenom('User');
    $utilisateur->setPassword('password');
    $utilisateur->setVilleResidence($ville);

    $this->entityManager->persist($utilisateur);
    $this->entityManager->flush();

    // Tester la recherche
    $found = $this->repository->findOneBy(['email' => 'test@example.com']);

    $this->assertNotNull($found);
    $this->assertEquals('test@example.com', $found->getEmail());
    $this->assertEquals('Test', $found->getNom());
  }

  public function testFindValidatedUsers(): void
  {
    // Cette méthode devrait être ajoutée au repository si elle n'existe pas
    $validatedUsers = $this->repository->findBy(['estValide' => true]);

    $this->assertIsArray($validatedUsers);

    foreach ($validatedUsers as $user) {
      $this->assertTrue($user->isEstValide());
    }
  }

  protected function tearDown(): void
  {
    parent::tearDown();
    $this->entityManager->close();
  }
}