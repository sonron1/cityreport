<?php

namespace App\Service;

class TokenService
{
  public function generateSecureToken(int $length = 32): string
  {
    #return bin2hex(random_bytes($length));
    return bin2hex(random_bytes($length/2));
  }

  public function isValidTokenFormat(string $token): bool
  {
    // Vérifier que le token est hexadécimal et a la bonne longueur
    return ctype_xdigit($token) && strlen($token) === 64;
  }
}