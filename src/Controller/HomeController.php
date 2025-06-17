<?php

namespace App\Controller;

use App\Repository\SignalementRepository;
use App\Repository\VilleRepository;
use App\Service\PerformanceOptimizationService;
use App\Enum\StatutSignalement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        SignalementRepository $signalementRepository, 
        VilleRepository $villeRepository,
        PerformanceOptimizationService $optimizationService
    ): Response {
        // ✅ Utilisation du cache pour optimiser les performances
        $cachedData = $optimizationService->warmupCache();
        
        // Récupérer les derniers signalements validés avec cache
        $derniersSignalements = $signalementRepository->findBy(
            ['statut' => StatutSignalement::NOUVEAU],
            ['dateSignalement' => 'DESC'],
            6 // Augmenté à 6 pour la nouvelle page
        );
        
        // Récupérer les villes avec cache
        $villes = $villeRepository->findAll();
        
        return $this->render('home/index.html.twig', [
            'derniers_signalements' => $derniersSignalements,
            'villes' => $villes,
            'cached_stats' => $cachedData['stats'] ?? [],
            'popular_categories' => $cachedData['categories'] ?? [],
        ]);
    }
}