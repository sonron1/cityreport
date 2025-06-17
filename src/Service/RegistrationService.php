<?php

namespace App\Service;

use App\Entity\Utilisateur;

class RegistrationService
{
  public function prepareUserForRegistration(Utilisateur $user): void
  {
    $user->setEstValide(false);
    // Autres préparations si nécessaire
  }

  public function validateRegistrationData(array $data): array
  {
    $errors = [];

    // Validations personnalisées
    if (empty($data['email'])) {
      $errors[] = 'Email requis';
    }

    return $errors;
  }
}