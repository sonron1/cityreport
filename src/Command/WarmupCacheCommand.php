<?php

namespace App\Command;

use App\Service\PerformanceOptimizationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cache:warmup-custom',
    description: 'Préchauffe le cache de l\'application'
)]
class WarmupCacheCommand extends Command
{
  public function __construct(
      private PerformanceOptimizationService $optimizationService
  ) {
    parent::__construct();
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    $io->title('🚀 Préchauffage du cache');

    $this->optimizationService->warmupCache();

    $io->success('✅ Cache préchauffé avec succès !');

    return Command::SUCCESS;
  }
}