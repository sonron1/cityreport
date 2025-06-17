<?php

namespace App\Enum;

enum EtatValidation: string
{
    case EN_ATTENTE = 'en_attente';
    case VALIDE = 'validé';  // ✅ CORRECTION : avec accent pour correspondre à la BD
    case REJETE = 'rejeté';  // ✅ CORRECTION : avec accent pour correspondre à la BD

    public function getLabel(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente',
            self::VALIDE => 'Validé',
            self::REJETE => 'Rejeté',
        };
    }

    public function getBadgeClass(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'bg-warning',
            self::VALIDE => 'bg-success',
            self::REJETE => 'bg-danger',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'clock',
            self::VALIDE => 'check-circle',
            self::REJETE => 'x-circle',
        };
    }
}