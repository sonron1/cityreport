<?php

namespace App\Service;

use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Psr\Log\LoggerInterface;

class SecurityService
{
  public function __construct(
      private RateLimiterFactory $apiLimiter,
      private Security $security,
      private LoggerInterface $logger
  ) {}

  /**
   * ✅ Protection contre le spam
   */
  public function checkRateLimit(Request $request, string $identifier = null): bool
  {
    $key = $identifier ?? $request->getClientIp();
    $limiter = $this->apiLimiter->create($key);

    if (!$limiter->consume(1)->isAccepted()) {
      $this->logger->warning('Rate limit dépassé', [
          'ip' => $request->getClientIp(),
          'user_agent' => $request->headers->get('User-Agent'),
          'identifier' => $key
      ]);
      return false;
    }

    return true;
  }

  /**
   * ✅ Validation des uploads
   */
  public function validateUpload(\SplFileInfo $file): array
  {
    $errors = [];

    // Taille max (5MB)
    if ($file->getSize() > 5 * 1024 * 1024) {
      $errors[] = 'Fichier trop volumineux (max 5MB)';
    }

    // Extensions autorisées
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower($file->getExtension());

    if (!in_array($extension, $allowedExtensions)) {
      $errors[] = 'Extension non autorisée';
    }

    // Validation MIME type
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file->getPathname());
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($mimeType, $allowedMimes)) {
      $errors[] = 'Type de fichier non autorisé';
    }

    return $errors;
  }

  /**
   * ✅ Nettoyage des inputs
   */
  public function sanitizeInput(string $input): string
  {
    // Supprime les scripts et balises dangereuses
    $input = strip_tags($input, '<p><br><strong><em><ul><ol><li>');

    // Échappe les caractères HTML
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    return trim($input);
  }
}