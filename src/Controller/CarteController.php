<?php

namespace App\Controller;

use App\Repository\ArrondissementRepository;
use App\Repository\CategorieRepository;
use App\Repository\SignalementRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CarteController extends AbstractController
{
    #[Route('/carte', name: 'app_carte')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        VilleRepository $villeRepository, 
        CategorieRepository $categorieRepository,
        ArrondissementRepository $arrondissementRepository
    ): Response
    {
        $user = $this->getUser();
        
        // Vérifier que l'utilisateur est validé
        if (!$user->isEstValide()) {
            $this->addFlash('error', 'Votre compte doit être validé pour accéder à la carte.');
            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('carte/index.html.twig', [
            'villes' => $villeRepository->findAll(),
            'categories' => $categorieRepository->findAll(),
            'arrondissements' => $arrondissementRepository->findAll(),
        ]);
    }
}