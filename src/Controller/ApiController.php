<?php

namespace App\Controller;

use App\Entity\Arrondissement;
use App\Enum\EtatValidation;
use App\Repository\VilleRepository;
use App\Repository\SignalementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    // ... autres méthodes inchangées ...

    /**
     * Récupérer les signalements avec possibilité de filtrer
     */
    #[Route('/signalements', name: 'api_signalements', methods: ['GET'])]
    public function getSignalements(
        Request $request,
        SignalementRepository $signalementRepository
    ): JsonResponse
    {
        try {
            // Récupérer les paramètres de filtrage
            $villeId = $request->query->get('ville');
            $categorieId = $request->query->get('categorie');
            $etat = $request->query->get('etat', EtatValidation::VALIDE->value);
            $dateDu = $request->query->get('date_du');
            $dateAu = $request->query->get('date_au');

            // Préparer les données pour la requête personnalisée
            $filters = [
                'ville' => $villeId,
                'categorie' => $categorieId,
                'etat' => $etat
            ];

            // Ajouter les dates seulement si elles sont présentes
            if ($dateDu) {
                $filters['date_du'] = new \DateTime($dateDu);
            }

            if ($dateAu) {
                $filters['date_au'] = new \DateTime($dateAu . ' 23:59:59');
            }

            // Récupérer les signalements filtrés
            $signalements = $signalementRepository->findByFilters($filters);

            // Formater les données pour la réponse JSON
            $data = [];
            foreach ($signalements as $signalement) {
                // Vérifier si les coordonnées sont définies
                if ($signalement->getLatitude() && $signalement->getLongitude()) {
                    $data[] = [
                        'id' => $signalement->getId(),
                        'titre' => $signalement->getTitre(),
                        'description' => $signalement->getDescription(),
                        'lat' => $signalement->getLatitude(),
                        'lng' => $signalement->getLongitude(),
                        'date' => $signalement->getDateSignalement()->format('Y-m-d H:i'),
                        'etat' => $signalement->getEtatValidation(),
                        'categorie' => $signalement->getCategorie() ? [
                            'id' => $signalement->getCategorie()->getId(),
                            'nom' => $signalement->getCategorie()->getNom(),
                            'icone' => $signalement->getCategorie()->getIcone(),
                            'couleur' => $signalement->getCategorie()->getCouleur()
                        ] : null,
                        'ville' => $signalement->getVille() ? $signalement->getVille()->getNom() : null,
                        'photoUrl' => $signalement->getPhotoUrl(),
                        'images' => $signalement->getPhotoUrl() ? [
                            'url' => $signalement->getPhotoUrl()
                        ] : null
                    ];
                }
            }

            return new JsonResponse($data);
        } catch (\Exception $e) {
            // Retourner une erreur détaillée pour faciliter le débogage
            return new JsonResponse([
                'error' => true,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

  #[Route('/arrondissements-by-ville/{villeId}', name: 'api_arrondissements_by_ville')]
    public function getArrondissementsByVille(int $villeId, VilleRepository $villeRepository): JsonResponse
    {
      $ville = $villeRepository->find($villeId);

      if (!$ville) {
        return $this->json([], Response::HTTP_NOT_FOUND);
      }

      $arrondissements = [];
      foreach ($ville->getArrondissements() as $arrondissement) {
        $arrondissements[] = [
            'id' => $arrondissement->getId(),
            'nom' => $arrondissement->getNom()
        ];
      }

      return $this->json($arrondissements);
    }

  #[Route('/api/arrondissement/{id}', name: 'api_arrondissement', methods: ['GET'])]
  public function getArrondissement(Arrondissement $arrondissement): JsonResponse
  {
    // Récupérer les données de l'arrondissement et de sa ville
    $data = [
        'id' => $arrondissement->getId(),
        'nom' => $arrondissement->getNom(),
        'ville' => [
            'id' => $arrondissement->getVille()->getId(),
            'nom' => $arrondissement->getVille()->getNom()
        ]
    ];

    return $this->json($data);
  }


}