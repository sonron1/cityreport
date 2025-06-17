<?php

namespace App\Controller;

use App\Entity\Reparation;
use App\Entity\Signalement;
use App\Form\ReparationTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReparationController extends AbstractController
{
  #[Route('/reparation/new/{signalement_id}', name: 'app_reparation_new', requirements: ['signalement_id' => '\d+'])]
  #[IsGranted('ROLE_MODERATOR')]
  public function new(Request $request, EntityManagerInterface $entityManager, int $signalement_id): Response
  {
    // Récupérer le signalements manuellement depuis la base de données
    $signalement = $entityManager->getRepository(Signalement::class)->find($signalement_id);

    if (!$signalement) {
      $this->addFlash('error', 'Signalement non trouvé avec l\'ID ' . $signalement_id);
      return $this->redirectToRoute('app_signalements');
    }

    // Vérifier si le signalements a déjà une réparation
    if ($signalement->getReparation()) {
      $this->addFlash('warning', 'Ce signalements a déjà une réparation planifiée.');
      return $this->redirectToRoute('app_signalement_show', ['id' => $signalement_id]);
    }

    $reparation = new Reparation();
    $reparation->setSignalement($signalement);

    // Optionnel : assigner l'utilisateur connecté comme technicien
    if ($this->getUser()) {
      $reparation->setUtilisateur($this->getUser());
    }

    $form = $this->createForm(ReparationTypeForm::class, $reparation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      try {
        $entityManager->persist($reparation);
        $entityManager->flush();

        $this->addFlash('success', 'La réparation a été planifiée avec succès.');
        return $this->redirectToRoute('app_signalement_show', ['id' => $signalement_id]);
      } catch (\Exception $e) {
        $this->addFlash('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
      }
    }

    return $this->render('reparation/new.html.twig', [
        'form' => $form->createView(),
        'signalement' => $signalement  // ✅ Clé corrigée
    ]);
  }

  #[Route('/reparation/edit/{id}', name: 'app_reparation_edit', requirements: ['id' => '\d+'])]
  #[IsGranted('ROLE_MODERATOR')]
  public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
  {
    // Récupérer la réparation manuellement
    $reparation = $entityManager->getRepository(Reparation::class)->find($id);

    if (!$reparation) {
      $this->addFlash('error', 'Réparation non trouvée.');
      return $this->redirectToRoute('app_reparations');
    }

    $form = $this->createForm(ReparationTypeForm::class, $reparation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      try {
        $entityManager->flush();

        $this->addFlash('success', 'La réparation a été mise à jour avec succès.');
        return $this->redirectToRoute('app_signalement_show', ['id' => $reparation->getSignalement()->getId()]);
      } catch (\Exception $e) {
        $this->addFlash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
      }
    }

    return $this->render('reparation/edit.html.twig', [
        'form' => $form->createView(),
        'reparation' => $reparation,
        'signalement' => $reparation->getSignalement()  // ✅ Clé corrigée
    ]);
  }

  #[Route('/reparations', name: 'app_reparations')]
  #[IsGranted('ROLE_MODERATOR')]
  public function index(EntityManagerInterface $entityManager): Response
  {
    $reparations = $entityManager->getRepository(Reparation::class)->findAll();

    return $this->render('reparation/index.html.twig', [
        'reparations' => $reparations,
    ]);
  }
}