<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
  require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
  (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// Configuration spécifique aux tests
if ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) {
  // Déjà configuré
} else {
  $_ENV['APP_ENV'] = $_SERVER['APP_ENV'] = 'test';
}

// Forcer l'environnement de test
$_ENV['APP_ENV'] = $_SERVER['APP_ENV'] = 'test';