<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerSimpleTest extends WebTestCase
{
  public function testLoginPageLoads(): void
  {
    $client = static::createClient();
    $client->request('GET', '/login');

    $this->assertResponseIsSuccessful();
    $this->assertSelectorExists('form');
  }

  public function testRegisterPageLoads(): void
  {
    $client = static::createClient();
    $client->request('GET', '/register');

    $this->assertResponseIsSuccessful();
    $this->assertSelectorExists('form');
  }

  public function testLogoutRedirects(): void
  {
    $client = static::createClient();
    $client->request('GET', '/logout');

    $this->assertTrue($client->getResponse()->isRedirection());
  }

  public function testNotVerifiedPageHandling(): void
  {
    $client = static::createClient();
    $client->request('GET', '/compte-non-verifie');

    // Doit soit être accessible soit rediriger
    $statusCode = $client->getResponse()->getStatusCode();
    $this->assertTrue(
        $statusCode === 200 || ($statusCode >= 300 && $statusCode < 400),
        'La page doit être accessible ou rediriger'
    );
  }

  public function testCheckVerificationRoute(): void
  {
    $client = static::createClient();
    $client->request('GET', '/check-verification');

    // Cette route doit rediriger
    $this->assertTrue($client->getResponse()->isRedirection());
  }
}