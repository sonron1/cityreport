<?php
// src/Enum/DemandeSuppressionStatut.php
namespace App\Enum;

enum DemandeSuppressionStatut: string
{
    case DEMANDEE = 'demandee';
    case APPROUVEE = 'approuvee';
    case REJETEE = 'rejetee';
}