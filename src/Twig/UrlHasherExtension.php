<?php

namespace App\Twig;

use App\Service\UrlHasher;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Extension Twig pour simplifier le hashage d'IDs dans les templates
 * Permet d'utiliser : {{ signalement.id|hash_id }}
 */
class UrlHasherExtension extends AbstractExtension
{
  public function __construct(private UrlHasher $urlHasher)
  {}

  public function getFilters(): array
  {
    return [
        new TwigFilter('hash_id', [$this, 'hashId']),
    ];
  }

  public function hashId(int $id): string
  {
    return $this->urlHasher->encode($id);
  }
}