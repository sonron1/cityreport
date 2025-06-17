<?php

namespace App\Twig;

use App\Enum\StatutSignalement;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EnumExtension extends AbstractExtension
{
  public function getFunctions(): array
  {
    return [
        new TwigFunction('statut_signalement_values', [$this, 'getStatutSignalementValues']),
    ];
  }

  public function getStatutSignalementValues(): array
  {
    return array_map(fn($case) => $case->value, StatutSignalement::cases());
  }
}