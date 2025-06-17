<?php

namespace App\Enum;

enum StatutSignalement: string
{
    case NOUVEAU = 'nouveau';
    case EN_COURS = 'en_cours';
    case RESOLU = 'resolu';
    case ANNULE = 'annule';
}