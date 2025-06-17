<?php

namespace App\Tests\Unit\Service;

use App\Service\TokenService;
use PHPUnit\Framework\TestCase;

class TokenServiceTest extends TestCase
{
  private TokenService $tokenService;

  protected function setUp(): void
  {
    $this->tokenService = new TokenService();
  }

  public function testGenerateSecureToken(): void
  {
    $token = $this->tokenService->generateSecureToken();

    $this->assertIsString($token);
    $this->assertEquals(32, strlen($token));
    $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $token);
  }

  public function testGenerateUniqueTokens(): void
  {
    $token1 = $this->tokenService->generateSecureToken();
    $token2 = $this->tokenService->generateSecureToken();

    $this->assertNotEquals($token1, $token2, 'Les tokens générés doivent être uniques');
  }

  public function testTokenFormat(): void
  {
    $token = $this->tokenService->generateSecureToken();

    // Vérifier que le token ne contient que des caractères hexadécimaux
    $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $token);

    // Vérifier qu'il n'y a pas de caractères spéciaux
    $this->assertDoesNotMatchRegularExpression('/[^0-9a-f]/', $token);
  }
}