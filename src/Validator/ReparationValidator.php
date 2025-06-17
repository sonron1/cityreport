<?php

namespace App\Validator;

use App\Entity\Reparation;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ReparationValidator
{
    public static function validateDates(Reparation $reparation, ExecutionContextInterface $context): void
    {
        $dateDebut = $reparation->getDateDebut();
        $dateFin = $reparation->getDateFin();

        // Si les deux dates sont définies
        if ($dateDebut && $dateFin) {
            // Vérifier que la date de début n'est pas postérieure à la date de fin
            if ($dateDebut > $dateFin) {
                $context->buildViolation('La date de début ne peut pas être postérieure à la date de fin.')
                    ->atPath('dateDebut')
                    ->addViolation();
            }

            // Vérifier qu'il y a au moins une heure entre les deux dates
            $interval = $dateDebut->diff($dateFin);
            $totalMinutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
            
            if ($totalMinutes < 60) {
                $context->buildViolation('La durée de la réparation doit être d\'au moins une heure.')
                    ->atPath('dateFin')
                    ->addViolation();
            }
        }
    }
}