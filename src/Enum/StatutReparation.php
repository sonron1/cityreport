<?php

namespace App\Enum;

enum StatutReparation: string
{
    case PLANIFIEE = 'planifiee';
    case EN_COURS = 'en_cours';
    case TERMINEE = 'terminee';
    case ANNULEE = 'annulee';
    case SUSPENDUE = 'suspendue';

    public function getLabel(): string
    {
        return match($this) {
            self::PLANIFIEE => 'Planifiée',
            self::EN_COURS => 'En cours',
            self::TERMINEE => 'Terminée',
            self::ANNULEE => 'Annulée',
            self::SUSPENDUE => 'Suspendue',
        };
    }

    public function getBadgeClass(): string
    {
        return match($this) {
            self::PLANIFIEE => 'bg-warning',
            self::EN_COURS => 'bg-primary',
            self::TERMINEE => 'bg-success',
            self::ANNULEE => 'bg-danger',
            self::SUSPENDUE => 'bg-secondary',
        };
    }
}