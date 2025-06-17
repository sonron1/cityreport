<?php

namespace App\DataFixtures;

use App\Entity\Signalement;
use App\Enum\EtatValidation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SignalementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ... code existant ...
        
        // Utiliser l'enum pour l'état de validation
        //$signalements->setEtatValidation(EtatValidation::VALIDE->value);
        
        // ... suite du code ...
    }
}