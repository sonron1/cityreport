<?php

namespace App\Tests\Debug;

use App\Repository\SignalementRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CheckSignalementStatut extends KernelTestCase
{
  public function testCheckStatutInDatabase(): void
  {
    self::bootKernel();
    $container = static::getContainer();

    /** @var SignalementRepository $signalementRepo */
    $signalementRepo = $container->get(SignalementRepository::class);

    $signalements = $signalementRepo->findAll();

    foreach ($signalements as $signalement) {
      echo sprintf(
          "ID: %d, Titre: %s, Statut: %s\n",
          $signalement->getId(),
          $signalement->getTitre(),
          $signalement->getStatut() ? $signalement->getStatut()->value : 'NULL'
      );
    }

    $this->assertTrue(true); // Pour que le test passe
  }
}