<?php

namespace App\Controller;

use App\Entity\Arrondissement;
use App\Entity\Ville;
use App\Repository\ArrondissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/arrondissement')]
class ArrondissementController extends AbstractController
{
  #[Route('/', name: 'app_arrondissement_index', methods: ['GET'])]
  public function index(ArrondissementRepository $arrondissementRepository): Response
  {
    return $this->render('arrondissement/index.html.twig', [
        'arrondissements' => $arrondissementRepository->findAll(),
    ]);
  }

  #[Route('/by-ville/{id}', name: 'app_arrondissement_by_ville', methods: ['GET'])]
  public function getByVille(Ville $ville, ArrondissementRepository $arrondissementRepository): Response
  {
    $arrondissements = $arrondissementRepository->findBy(['ville' => $ville], ['nom' => 'ASC']);

    return $this->render('arrondissement/_list.html.twig', [
        'arrondissements' => $arrondissements,
    ]);
  }

  #[Route('/api/by-ville/{id}', name: 'app_arrondissement_api_by_ville', methods: ['GET'])]
  public function apiGetByVille(Ville $ville, ArrondissementRepository $arrondissementRepository): JsonResponse
  {
    $arrondissements = $arrondissementRepository->findBy(['ville' => $ville], ['nom' => 'ASC']);

    $data = [];
    foreach ($arrondissements as $arrondissement) {
      $data[] = [
          'id' => $arrondissement->getId(),
          'nom' => $arrondissement->getNom(),
          'slug' => $arrondissement->getSlug()
      ];
    }

    return new JsonResponse($data);
  }

  #[Route('/ajax-select', name: 'app_arrondissement_ajax_select', methods: ['POST'])]
  public function ajaxSelect(Request $request, EntityManagerInterface $entityManager): Response
  {
    // Vérifier que c'est une requête AJAX
    if (!$request->isXmlHttpRequest()) {
      return new JsonResponse(['error' => 'Requête non autorisée'], 400);
    }

    $villeId = $request->request->get('ville_id');
    if (!$villeId) {
      return new JsonResponse([
          'html' => $this->renderView('arrondissement/_select_empty.html.twig')
      ]);
    }

    $ville = $entityManager->getRepository(Ville::class)->find($villeId);
    if (!$ville) {
      return new JsonResponse(['error' => 'Ville non trouvée'], 404);
    }

    $arrondissements = $entityManager->getRepository(Arrondissement::class)
        ->findBy(['ville' => $ville], ['nom' => 'ASC']);

    return new JsonResponse([
        'html' => $this->renderView('arrondissement/_select_options.html.twig', [
            'arrondissements' => $arrondissements
        ])
    ]);
  }


}