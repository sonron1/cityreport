<?php
// src/Controller/CommentaireController.php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Signalement;
use App\Entity\JournalValidation;
use App\Form\CommentaireTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentaireController extends AbstractController
{
  #[Route('/commentaire/add/{signalement_id}', name: 'app_commentaire_add')]
  #[IsGranted('ROLE_USER')]
  public function add(
      Request $request,
      EntityManagerInterface $entityManager,
      int $signalement_id
  ): Response
  {
    // Récupérer le signalement à partir de l'ID
    $signalement = $entityManager->getRepository(Signalement::class)->find($signalement_id);

    if (!$signalement) {
      throw $this->createNotFoundException('Signalement non trouvé');
    }

    // Vérifier si l'utilisateur a déjà commenté ce signalement
    $existingComment = $entityManager->getRepository(Commentaire::class)->findOneBy([
        'utilisateur' => $this->getUser(),
        'signalement' => $signalement  // ✅ CORRECTION ICI : 'signalement' au lieu de 'signalements'
    ]);

    if ($existingComment) {
      $this->addFlash('warning', 'Vous avez déjà commenté ce signalement. Un seul commentaire par utilisateur est autorisé.');
      return $this->redirectToRoute('app_signalement_show', ['id' => $signalement_id]);
    }

    $commentaire = new Commentaire();
    $commentaire->setSignalement($signalement);
    $commentaire->setUtilisateur($this->getUser());
    $commentaire->setDateCommentaire(new \DateTime());

    $form = $this->createForm(CommentaireTypeForm::class, $commentaire);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->persist($commentaire);
      $entityManager->flush();

      $this->addFlash('success', 'Votre commentaire a été ajouté avec succès.');
    } else if ($form->isSubmitted()) {
      $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du commentaire.');
    }

    return $this->redirectToRoute('app_signalement_show', ['id' => $signalement_id]);
  }

  #[Route('/commentaire/delete/{id}', name: 'app_commentaire_delete')]
  public function delete(
      Commentaire $commentaire,
      EntityManagerInterface $entityManager
  ): Response
  {
    // Vérifier que l'utilisateur a les droits nécessaires
    if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_MODERATOR') && !$this->isGranted('ROLE_REPARATEUR')) {
      throw $this->createAccessDeniedException('Vous n\'avez pas les droits nécessaires pour supprimer ce commentaire.');
    }

    // Récupérer l'ID du signalement avant la suppression pour la redirection
    $signalementId = $commentaire->getSignalement()->getId();

    // Créer une entrée dans le journal
    $journal = new JournalValidation();
    $journal->setSignalement($commentaire->getSignalement());
    $journal->setModerateur($this->getUser());
    $journal->setDateValidation(new \DateTime());
    $journal->setAction('Suppression de commentaire');
    $journal->setCommentaire('Commentaire supprimé par ' . $this->getUser()->getPrenom() . ' ' . $this->getUser()->getNom());

    // Enregistrer le journal d'abord
    $entityManager->persist($journal);
    $entityManager->flush();

    try {
      // Supprimer le commentaire en détachant correctement les relations
      $utilisateur = $commentaire->getUtilisateur();
      $signalement = $commentaire->getSignalement();

      // Détacher des collections
      if ($utilisateur) {
        $utilisateur->removeCommentaire($commentaire);
      }

      if ($signalement) {
        $signalement->removeCommentaire($commentaire);
      }

      // Supprimer le commentaire
      $entityManager->remove($commentaire);
      $entityManager->flush();

      $this->addFlash('success', 'Le commentaire a été supprimé avec succès.');
    } catch (\Exception $e) {
      $this->addFlash('error', 'Erreur lors de la suppression du commentaire : ' . $e->getMessage());
    }

    // Rediriger vers la page du signalement
    return $this->redirectToRoute('app_signalement_show', [
        'id' => $signalementId,
        '_fragment' => 'commentaires'
    ]);
  }
}