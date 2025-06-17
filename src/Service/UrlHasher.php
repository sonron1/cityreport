<?php

namespace App\Service;

use Hashids\Hashids;

/**
 * Service pour chiffrer et déchiffrer les IDs dans les URLs
 * Transforme /signalement/123 en /signalement/aBc3De9f
 */
class UrlHasher
{
  private Hashids $hashids;

  public function __construct()
  {
    // Clé secrète à changer en production + longueur minimale de 8 caractères
    $this->hashids = new Hashids('a6ecd4f0bd899a187f05e95b627e1c55', 8);
  }

  /**
   * Encode un ID numérique en hash
   */
  public function encode(int $id): string
  {
    return $this->hashids->encode($id);
  }

  /**
   * Décode un hash en ID numérique
   * Retourne null si le hash est invalide
   */
  public function decode(string $hash): ?int
  {
    $decoded = $this->hashids->decode($hash);
    return !empty($decoded) ? $decoded[0] : null;
  }
}