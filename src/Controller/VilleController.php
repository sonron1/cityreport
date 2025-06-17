<?php
// src/Controller/VilleController.php

namespace App\Controller;

use App\Repository\VilleRepository;
use App\Repository\SignalementRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VilleController extends AbstractController
{
  #[Route('/ville/{slug}', name: 'app_ville_show')]
  public function show(
      string $slug,
      Request $request,
      VilleRepository $villeRepository,
      SignalementRepository $signalementRepository,
      CategorieRepository $categorieRepository
  ): Response {
    // Récupérer la ville par son slug
    $ville = $villeRepository->findOneBy(['slug' => $slug]);

    if (!$ville) {
      throw new NotFoundHttpException('Ville non trouvée');
    }

    // Récupérer les signalements validés pour cette ville avec pagination
    $page = max(1, $request->query->getInt('page', 1));
    $itemsPerPage = 9; // 9 signalements par page (3x3)

    // Filtres spécifiques à la ville
    $filters = [
        'ville' => $ville->getId(),
        'statut' => $request->query->get('statut'),
        'categorie' => $request->query->has('categorie') ? $request->query->get('categorie') : null,
        'date' => $request->query->get('date'),
    ];

    // Filtrez les valeurs vides
    $filters = array_filter($filters, function ($value) {
      return $value !== null && $value !== '';
    });

    // Récupérer les signalements de la ville
    $signalements = $signalementRepository->findAllPublicSignalementsPaginated(
        $page,
        $itemsPerPage,
        $filters
    );

    // Récupérer tous les signalements validés de la ville pour la carte
    $allSignalementsForMap = $signalementRepository->findByFilters([
        'ville' => $ville->getId(),
        'etat' => 'validé'
    ]);

    // Récupérer les catégories pour les filtres
    $categories = $categorieRepository->findAll();

    return $this->render('ville/show.html.twig', [
        'ville' => $ville,
        'signalements_paginator' => $signalements,
        'all_signalements_for_map' => $allSignalementsForMap,
        'categories' => $categories,
    ]);
  }
}