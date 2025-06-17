<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\JournalValidation;
use App\Entity\Signalement;
use App\Entity\Utilisateur;
use App\Enum\EtatValidation;
use App\Enum\StatutSignalement;
use App\Form\CategorieType;
use App\Form\UtilisateurType;
use App\Form\VilleType;
use App\Repository\CategorieRepository;
use App\Repository\DepartementRepository;
use App\Repository\JournalValidationRepository;
use App\Repository\SignalementRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VilleRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
  #[Route('', name: 'app_admin_dashboard')]
  public function dashboard(
      SignalementRepository       $signalementRepository,
      UtilisateurRepository       $utilisateurRepository,
      JournalValidationRepository $journalRepository
  ): Response
  {
    // Statistiques simples
    $stats = [
        'total_signalements' => $signalementRepository->count([]),
        'signalements_en_attente' => $signalementRepository->count(['etatValidation' => EtatValidation::EN_ATTENTE->value]),
        'signalements_valides' => $signalementRepository->count(['etatValidation' => EtatValidation::VALIDE->value]),
        'signalements_rejetes' => $signalementRepository->count(['etatValidation' => EtatValidation::REJETE->value]),
        'total_utilisateurs' => $utilisateurRepository->count([]),
        'utilisateurs_actifs' => $utilisateurRepository->count(['estValide' => true]),
        'utilisateursNonValides' => $utilisateurRepository->count(['estValide' => false]),
        'demandesSuppressions' => 0,
    ];

    // Signalements et utilisateurs récents
    $signalements_recents = $signalementRepository->findBy([], ['dateSignalement' => 'DESC'], 5);
    $utilisateurs_recents = $utilisateurRepository->findBy([], ['dateInscription' => 'DESC'], 5);

    return $this->render('admin/dashboard.html.twig', [
        'stats' => $stats,
        'signalements_recents' => $signalements_recents,
        'utilisateurs_recents' => $utilisateurs_recents,
    ]);
  }

  #[Route('/statistiques', name: 'app_admin_statistiques')]
  public function statistiques(
      SignalementRepository $signalementRepository,
      UtilisateurRepository $utilisateurRepository,
      VilleRepository $villeRepository,
      CategorieRepository $categorieRepository,
      JournalValidationRepository $journalRepository,
      EntityManagerInterface $entityManager
  ): Response {

    $stats = [
      // Statistiques principales
        'total_signalements' => $signalementRepository->count([]),
        'signalements_en_attente' => $signalementRepository->count(['etatValidation' => EtatValidation::EN_ATTENTE->value]),
        'signalements_valides' => $signalementRepository->count(['etatValidation' => EtatValidation::VALIDE->value]),
        'signalements_rejetes' => $signalementRepository->count(['etatValidation' => EtatValidation::REJETE->value]),
        'total_utilisateurs' => $utilisateurRepository->count([]),
        'utilisateurs_valides' => $utilisateurRepository->count(['estValide' => true]),
        'utilisateurs_en_attente' => $utilisateurRepository->count(['estValide' => false]),

      // Données pour les graphiques
        'signalements_par_mois' => $this->getSignalementsParMois($entityManager),
        'signalements_par_statut' => $this->getSignalementsParStatut($entityManager),
        'signalements_par_ville' => $this->getSignalementsParVille($entityManager),
        'signalements_par_categorie' => $this->getSignalementsParCategorie($entityManager),
        'evolution_signalements' => $this->getEvolutionMensuelle($entityManager, 'signalements'),
        'evolution_utilisateurs' => $this->getEvolutionMensuelle($entityManager, 'utilisateurs'),

    ];

    $evolutionMensuelle = $this->getEvolutionMensuelle($entityManager, 'signalements');
    $recent_activities = $this->getRecentActivities($signalementRepository, $utilisateurRepository);

    return $this->render('admin/statistiques/index.html.twig', [
        'stats' => $stats,
        'evolution_mensuelle' => $evolutionMensuelle,
        'activites_recentes' => $this->getActivitesRecentes($signalementRepository, $journalRepository),
        'recent_activities' => $recent_activities,
    ]);

  }


  #[Route('/utilisateurs', name: 'app_admin_utilisateurs')]
  public function utilisateurs(
      UtilisateurRepository $utilisateurRepository,
      Request               $request
  ): Response
  {
    $search = $request->query->get('search', '');
    $status = $request->query->get('status', '');
    $role = $request->query->get('role', '');
    $tri = $request->query->get('tri', 'inscription_desc');

    $queryBuilder = $utilisateurRepository->createQueryBuilder('u');

    if (!empty($search)) {
      $queryBuilder->andWhere('u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search')
          ->setParameter('search', '%' . $search . '%');
    }

    if ($status === 'valide') {
      $queryBuilder->andWhere('u.estValide = true');
    } elseif ($status === 'non_valide') {
      $queryBuilder->andWhere('u.estValide = false');
    }

    // Pas de filtre par rôle dans la requête SQL pour éviter les problèmes JSON

    // Tri
    switch ($tri) {
      case 'inscription_asc':
        $queryBuilder->orderBy('u.dateInscription', 'ASC');
        break;
      case 'nom_asc':
        $queryBuilder->orderBy('u.nom', 'ASC');
        break;
      case 'nom_desc':
        $queryBuilder->orderBy('u.nom', 'DESC');
        break;
      default:
        $queryBuilder->orderBy('u.dateInscription', 'DESC');
    }

    $utilisateurs = $queryBuilder->getQuery()->getResult();

    // Filtrer par rôle côté PHP si nécessaire
    if (!empty($role)) {
      $utilisateurs = array_filter($utilisateurs, function($utilisateur) use ($role) {
        return in_array($role, $utilisateur->getRoles());
      });

      // Réindexer le tableau après filtrage
      $utilisateurs = array_values($utilisateurs);
    }

    // Statistiques simples
    $user_stats = [
        'total' => $utilisateurRepository->count([]),
        'valides' => $utilisateurRepository->count(['estValide' => true]),
        'non_valides' => $utilisateurRepository->count(['estValide' => false]),
        'moderateurs' => $this->countModerators($utilisateurs),
        'admins' => $this->countAdmins($utilisateurs),
        'citoyens' => $this->countUsers($utilisateurs),
    ];

    return $this->render('admin/users/index.html.twig', [
        'utilisateurs' => $utilisateurs,
        'user_stats' => $user_stats,
        'current_filters' => [
            'search' => $search,
            'status' => $status,
            'role' => $role,
            'tri' => $tri,
        ],
    ]);
  }

  #[Route('/users', name: 'app_admin_users')]
  public function users(
      UtilisateurRepository $utilisateurRepository,
      Request               $request
  ): Response
  {
    return $this->redirectToRoute('app_admin_utilisateurs', $request->query->all());
  }

  // ========================
  // GESTION DES UTILISATEURS
  // ========================

  #[Route('/user/new', name: 'app_admin_user_new')]
  public function newUser(
      Request                     $request,
      EntityManagerInterface      $entityManager,
      UserPasswordHasherInterface $passwordHasher
  ): Response
  {
    $utilisateur = new Utilisateur();
    $form = $this->createForm(UtilisateurType::class, $utilisateur);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Hash du mot de passe
      if ($form->get('plainPassword')->getData()) {
        $hashedPassword = $passwordHasher->hashPassword(
            $utilisateur,
            $form->get('plainPassword')->getData()
        );
        $utilisateur->setPassword($hashedPassword);
      }

      $utilisateur->setDateInscription(new \DateTime());
      $entityManager->persist($utilisateur);
      $entityManager->flush();

      $this->addFlash('success', 'Utilisateur créé avec succès.');
      return $this->redirectToRoute('app_admin_users');
    }

    return $this->render('admin/users/new.html.twig', [
        'form' => $form->createView(),
    ]);
  }

  #[Route('/user/{id}/edit', name: 'app_admin_user_edit')]
  public function editUser(
      int                         $id,
      Request                     $request,
      UtilisateurRepository       $utilisateurRepository,
      EntityManagerInterface      $entityManager,
      UserPasswordHasherInterface $passwordHasher
  ): Response
  {
    $utilisateur = $utilisateurRepository->find($id);

    if (!$utilisateur) {
      throw $this->createNotFoundException('Utilisateur introuvable.');
    }

    $form = $this->createForm(UtilisateurType::class, $utilisateur);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Hash du nouveau mot de passe si fourni
      if ($form->get('plainPassword')->getData()) {
        $hashedPassword = $passwordHasher->hashPassword(
            $utilisateur,
            $form->get('plainPassword')->getData()
        );
        $utilisateur->setPassword($hashedPassword);
      }

      $entityManager->flush();

      $this->addFlash('success', 'Utilisateur modifié avec succès.');
      return $this->redirectToRoute('app_admin_users');
    }

    return $this->render('admin/users/edit.html.twig', [
        'utilisateur' => $utilisateur,
        'form' => $form->createView(),
    ]);
  }

  #[Route('/user/{id}/profile', name: 'app_admin_user_profile')]
  public function userProfile(int $id, UtilisateurRepository $utilisateurRepository): Response
  {
    $utilisateur = $utilisateurRepository->find($id);

    if (!$utilisateur) {
      throw $this->createNotFoundException('Utilisateur introuvable.');
    }

    return $this->render('admin/users/profile.html.twig', [
        'utilisateur' => $utilisateur,
    ]);
  }

  #[Route('/user/{id}/validate', name: 'app_admin_user_validate', methods: ['POST'])]
  public function validateUser(
      int                    $id,
      UtilisateurRepository  $utilisateurRepository,
      EntityManagerInterface $entityManager,
      Request                $request
  ): Response
  {
    $utilisateur = $utilisateurRepository->find($id);

    if (!$utilisateur) {
      $this->addFlash('error', 'Utilisateur introuvable.');
      return $this->redirectToRoute('app_admin_users');
    }

    if (!$this->isCsrfTokenValid('validate_user' . $utilisateur->getId(), $request->request->get('_token'))) {
      $this->addFlash('error', 'Token de sécurité invalide.');
      return $this->redirectToRoute('app_admin_users');
    }

    try {
      $utilisateur->setEstValide(true);
      $entityManager->flush();

      $this->addFlash('success', "Le compte de {$utilisateur->getPrenom()} {$utilisateur->getNom()} a été validé.");
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue lors de la validation.');
    }

    return $this->redirectToRoute('app_admin_users');
  }

  #[Route('/user/{id}/login-as', name: 'app_admin_user_login_as')]
  public function loginAsUser(int $id, UtilisateurRepository $utilisateurRepository): Response
  {
    $utilisateur = $utilisateurRepository->find($id);

    if (!$utilisateur) {
      throw $this->createNotFoundException('Utilisateur introuvable.');
    }

    // Note: Cette fonctionnalité nécessite une configuration spéciale dans security.yaml
    $this->addFlash('info', 'Fonctionnalité disponible avec la configuration switch_user dans security.yaml');
    return $this->redirectToRoute('app_admin_users');
  }

  #[Route('/user/{id}/reset-password', name: 'app_admin_user_reset_password')]
  public function resetUserPassword(
      int                         $id,
      UtilisateurRepository       $utilisateurRepository,
      EntityManagerInterface      $entityManager,
      UserPasswordHasherInterface $passwordHasher
  ): Response
  {
    $utilisateur = $utilisateurRepository->find($id);

    if (!$utilisateur) {
      throw $this->createNotFoundException('Utilisateur introuvable.');
    }

    // Générer un mot de passe temporaire
    $tempPassword = bin2hex(random_bytes(8));
    $hashedPassword = $passwordHasher->hashPassword($utilisateur, $tempPassword);
    $utilisateur->setPassword($hashedPassword);

    $entityManager->flush();

    $this->addFlash('success', "Mot de passe réinitialisé. Nouveau mot de passe temporaire : {$tempPassword}");
    return $this->redirectToRoute('app_admin_users');
  }

  #[Route('/user/{id}/suspend', name: 'app_admin_user_suspend')]
  public function suspendUser(
      int                    $id,
      UtilisateurRepository  $utilisateurRepository,
      EntityManagerInterface $entityManager
  ): Response
  {
    $utilisateur = $utilisateurRepository->find($id);

    if (!$utilisateur) {
      throw $this->createNotFoundException('Utilisateur introuvable.');
    }

    $utilisateur->setEstValide(false);
    $entityManager->flush();

    $this->addFlash('success', "Le compte de {$utilisateur->getPrenom()} {$utilisateur->getNom()} a été suspendu.");
    return $this->redirectToRoute('app_admin_users');
  }

  #[Route('/user/export', name: 'app_admin_user_export')]
  public function exportUsers(UtilisateurRepository $utilisateurRepository): Response
  {
    $utilisateurs = $utilisateurRepository->findAll();

    $csvData = "Prénom,Nom,Email,Statut,Rôles,Date d'inscription\n";

    foreach ($utilisateurs as $utilisateur) {
      $csvData .= sprintf(
          "%s,%s,%s,%s,%s,%s\n",
          $utilisateur->getPrenom(),
          $utilisateur->getNom(),
          $utilisateur->getEmail(),
          $utilisateur->isEstValide() ? 'Validé' : 'En attente',
          implode('|', $utilisateur->getRoles()),
          $utilisateur->getDateInscription()->format('Y-m-d H:i:s')
      );
    }

    $response = new Response($csvData);
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition',
        $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'utilisateurs.csv')
    );

    return $response;
  }

  #[Route('/user/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
  #[IsGranted('ROLE_ADMIN')]
  public function delete(Utilisateur $utilisateur, Request $request, EntityManagerInterface $entityManager): Response
  {
    // Vérification du token CSRF
    if (!$this->isCsrfTokenValid('delete_user_' . $utilisateur->getId(), $request->request->get('_token'))) {
      throw $this->createAccessDeniedException('Token CSRF invalide.');
    }

    // Empêcher la suppression de son propre compte
    if ($utilisateur === $this->getUser()) {
      $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
      return $this->redirectToRoute('app_admin_users');
    }

    $nom = $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();

    try {
      $entityManager->remove($utilisateur);
      $entityManager->flush();

      $this->addFlash('success', 'L\'utilisateur ' . $nom . ' a été supprimé définitivement.');
    } catch (\Exception $e) {
      $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
    }

    return $this->redirectToRoute('app_admin_users');
  }


  // ========================
  // GESTION DES SIGNALEMENTS
  // ========================

  #[Route('/signalements', name: 'app_admin_signalements')]
  public function signalements(
      SignalementRepository $signalementRepository,
      CategorieRepository   $categorieRepository,
      VilleRepository       $villeRepository,
      Request               $request
  ): Response
  {
    $search = $request->query->get('search', '');
    $etat = $request->query->get('etat', '');
    $statut = $request->query->get('statut', '');
    $categorie = $request->query->get('categorie', '');
    $ville = $request->query->get('ville', '');
    $tri = $request->query->get('tri', 'date_desc');

    $queryBuilder = $signalementRepository->createQueryBuilder('s')
        ->leftJoin('s.utilisateur', 'u')
        ->leftJoin('s.categorie', 'c')
        ->leftJoin('s.ville', 'v');

    // Filtres de recherche
    if (!empty($search)) {
      $queryBuilder->andWhere('s.titre LIKE :search OR s.description LIKE :search OR u.nom LIKE :search OR u.prenom LIKE :search')
          ->setParameter('search', '%' . $search . '%');
    }

    if (!empty($etat)) {
      $queryBuilder->andWhere('s.etatValidation = :etat')
          ->setParameter('etat', $etat);
    }

    if (!empty($statut)) {
      $queryBuilder->andWhere('s.statut = :statut')
          ->setParameter('statut', $statut);
    }

    if (!empty($categorie)) {
      $queryBuilder->andWhere('s.categorie = :categorie')
          ->setParameter('categorie', $categorie);
    }

    if (!empty($ville)) {
      $queryBuilder->andWhere('s.ville = :ville')
          ->setParameter('ville', $ville);
    }

    // Tri
    switch ($tri) {
      case 'date_asc':
        $queryBuilder->orderBy('s.dateSignalement', 'ASC');
        break;
      case 'titre_asc':
        $queryBuilder->orderBy('s.titre', 'ASC');
        break;
      case 'titre_desc':
        $queryBuilder->orderBy('s.titre', 'DESC');
        break;
      case 'utilisateur_asc':
        $queryBuilder->orderBy('u.nom', 'ASC');
        break;
      case 'utilisateur_desc':
        $queryBuilder->orderBy('u.nom', 'DESC');
        break;
      default:
        $queryBuilder->orderBy('s.dateSignalement', 'DESC');
    }

    $signalements = $queryBuilder->getQuery()->getResult();

    // Récupérer toutes les catégories et villes pour les filtres
    $categories = $categorieRepository->findAll();
    $villes = $villeRepository->findAll();

    // Statistiques pour l'affichage
    $stats = [
        'total' => $signalementRepository->count([]),
        'en_attente' => $signalementRepository->count(['etatValidation' => EtatValidation::EN_ATTENTE->value]),
        'valides' => $signalementRepository->count(['etatValidation' => EtatValidation::VALIDE->value]),
        'rejetes' => $signalementRepository->count(['etatValidation' => EtatValidation::REJETE->value]),
        'nouveau' => $signalementRepository->count(['statut' => StatutSignalement::NOUVEAU->value]),
        'en_cours' => $signalementRepository->count(['statut' => StatutSignalement::EN_COURS->value]),
        'resolu' => $signalementRepository->count(['statut' => StatutSignalement::RESOLU->value]),
    ];

    return $this->render('admin/signalements/index.html.twig', [
        'signalements' => $signalements,
        'categories' => $categories,
        'villes' => $villes,
        'stats' => $stats,
        'current_filters' => [
            'search' => $search,
            'etat' => $etat,
            'statut' => $statut,
            'categorie' => $categorie,
            'ville' => $ville,
            'tri' => $tri,
        ],
    ]);
  }

  // ========================
  // ACTIONS SUR LES SIGNALEMENTS
  // ========================

  #[Route('/signalements/{id<\d+>}', name: 'app_admin_signalements_show')]
  public function showSignalement(Signalement $signalement): Response
  {
    return $this->render('admin/signalements/show.html.twig', [
        'signalement' => $signalement,
    ]);
  }

  #[Route('/signalements/{id<\d+>}/valider', name: 'app_admin_signalements_valider', methods: ['POST'])]
  public function validerSignalement(
      int                    $id,
      SignalementRepository  $signalementRepository,
      EntityManagerInterface $entityManager,
      Request                $request
  ): Response
  {
    $signalement = $signalementRepository->find($id);

    if (!$signalement) {
      $this->addFlash('error', 'Signalement introuvable.');
      return $this->redirectToRoute('app_admin_signalements');
    }

    if (!$this->isCsrfTokenValid('validate' . $signalement->getId(), $request->request->get('_token'))) {
      $this->addFlash('error', 'Token de sécurité invalide.');
      return $this->redirectToRoute('app_admin_signalements');
    }

    try {
      $signalement->setEtatValidation(EtatValidation::VALIDE);
      $signalement->setStatut(StatutSignalement::EN_COURS);

      // Créer une entrée dans le journal
      $journal = new JournalValidation();
      $journal->setSignalement($signalement);
      $journal->setModerateur($this->getUser());
      $journal->setDateValidation(new \DateTime());
      $journal->setAction('validé');
      $journal->setCommentaire($request->request->get('commentaire', 'Signalement validé par l\'administrateur'));

      $entityManager->persist($journal);
      $entityManager->flush();

      $this->addFlash('success', 'Signalement validé avec succès.');
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue lors de la validation.');
    }

    return $this->redirectToRoute('app_admin_signalements');
  }

  #[Route('/signalements/{id<\d+>}/rejeter', name: 'app_admin_signalements_rejeter', methods: ['POST'])]
  public function rejeterSignalement(
      int $id,
      SignalementRepository $signalementRepository,
      EntityManagerInterface $entityManager,
      NotificationService $notificationService,
      Request $request
  ): Response
  {
    $signalement = $signalementRepository->find($id);

    if (!$signalement) {
      $this->addFlash('error', 'Signalement introuvable.');
      return $this->redirectToRoute('app_admin_signalements');
    }

    if (!$this->isCsrfTokenValid('reject' . $signalement->getId(), $request->request->get('_token'))) {
      $this->addFlash('error', 'Token de sécurité invalide.');
      return $this->redirectToRoute('app_admin_signalements');
    }

    $commentaire = $request->request->get('commentaire', '');

    if (empty($commentaire)) {
      $this->addFlash('error', 'Un commentaire est obligatoire pour rejeter un signalement.');
      return $this->redirectToRoute('app_admin_signalements');
    }

    try {
      $signalement->setEtatValidation(EtatValidation::REJETE);

      // Créer une entrée dans le journal
      $journal = new JournalValidation();
      $journal->setSignalement($signalement);
      $journal->setModerateur($this->getUser());
      $journal->setDateValidation(new \DateTime());
      $journal->setAction('rejeté');
      $journal->setCommentaire($commentaire);

      $entityManager->persist($journal);
      $entityManager->flush();

      // ✅ NOUVEAU : Envoyer les notifications (email + message interne)
      $notificationService->notifySignalementRejected($signalement, $commentaire);

      $this->addFlash('success', 'Signalement rejeté avec succès. L\'utilisateur a été notifié.');
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue lors du rejet.');
    }

    return $this->redirectToRoute('app_admin_signalements');
  }

  #[Route('/signalements/{id<\d+>}/modifier-statut', name: 'app_admin_signalements_modifier_statut', methods: ['POST'])]
  public function modifierStatutSignalement(
      int                    $id,
      SignalementRepository  $signalementRepository,
      EntityManagerInterface $entityManager,
      Request                $request
  ): Response
  {
    $signalement = $signalementRepository->find($id);

    if (!$signalement) {
      $this->addFlash('error', 'Signalement introuvable.');
      return $this->redirectToRoute('app_admin_signalements');
    }

    if (!$this->isCsrfTokenValid('modify_status_' . $signalement->getId(), $request->request->get('_token'))) {
      $this->addFlash('error', 'Token de sécurité invalide.');
      return $this->redirectToRoute('app_admin_signalements');
    }

    $nouveauStatut = $request->request->get('statut');

    try {
      $statutEnum = StatutSignalement::tryFrom($nouveauStatut);
      if (!$statutEnum) {
        throw new \InvalidArgumentException('Statut invalide');
      }

      $signalement->setStatut($statutEnum);

      // Créer une entrée dans le journal
      $journal = new JournalValidation();
      $journal->setSignalement($signalement);
      $journal->setModerateur($this->getUser());
      $journal->setDateValidation(new \DateTime());
      $journal->setAction('Modification du statut');
      $journal->setCommentaire("Statut modifié vers : {$statutEnum->value}");

      $entityManager->persist($journal);
      $entityManager->flush();

      $this->addFlash('success', 'Statut modifié avec succès.');
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue lors de la modification du statut.');
    }

    return $this->redirectToRoute('app_admin_signalements');
  }

  #[Route('/signalements/{id<\d+>}/supprimer', name: 'app_admin_signalements_delete', methods: ['POST'])]
  public function supprimerSignalement(
      int                    $id,
      SignalementRepository  $signalementRepository,
      EntityManagerInterface $entityManager,
      Request                $request
  ): Response
  {
    $signalement = $signalementRepository->find($id);

    if (!$signalement) {
      $this->addFlash('error', 'Signalement introuvable.');
      return $this->redirectToRoute('app_admin_signalements');
    }

    // ✅ CORRECTION : Utilisez 'admin_delete' au lieu de 'delete'
    if (!$this->isCsrfTokenValid('admin_delete' . $signalement->getId(), $request->request->get('_token'))) {
      $this->addFlash('error', 'Token de sécurité invalide.');
      return $this->redirectToRoute('app_admin_signalements');
    }

    try {
      $titre = $signalement->getTitre();
      $entityManager->remove($signalement);
      $entityManager->flush();

      $this->addFlash('success', "Le signalement \"{$titre}\" a été supprimé définitivement.");
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue lors de la suppression.');
    }

    return $this->redirectToRoute('app_admin_signalements');
  }

  #[Route('/signalements/{id<\d+>}/supprimer-force', name: 'app_admin_signalements_delete_force', methods: ['POST'])]
  public function supprimerSignalementForce(
      int                    $id,
      SignalementRepository  $signalementRepository,
      EntityManagerInterface $entityManager,
      Request                $request
  ): Response
  {
    $signalement = $signalementRepository->find($id);

    if (!$signalement) {
      $this->addFlash('error', 'Signalement introuvable.');
      return $this->redirectToRoute('app_admin_signalements');
    }

    if (!$this->isCsrfTokenValid('force_delete' . $signalement->getId(), $request->request->get('_token'))) {
      $this->addFlash('error', 'Token de sécurité invalide.');
      return $this->redirectToRoute('app_admin_signalements');
    }

    try {
      $titre = $signalement->getTitre();

      // Supprimer les entrées du journal liées
      $journalEntries = $entityManager->getRepository(JournalValidation::class)
          ->findBy(['signalement' => $signalement]);

      foreach ($journalEntries as $entry) {
        $entityManager->remove($entry);
      }

      // Supprimer le signalement
      $entityManager->remove($signalement);
      $entityManager->flush();

      $this->addFlash('success', "Le signalement \"{$titre}\" et toutes ses données associées ont été supprimés définitivement.");
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue lors de la suppression forcée.');
    }

    return $this->redirectToRoute('app_admin_signalements');
  }


  private function generateCsvExport(array $signalements, string $filename): Response
  {
    // En-têtes CSV avec plus de détails
    $headers = [
        'ID',
        'Titre',
        'Description',
        'Date de signalement',
        'Ville',
        'Arrondissement',
        'Catégorie',
        'Utilisateur (Email)',
        'Utilisateur (Nom)',
        'Utilisateur (Prénom)',
        'Statut',
        'État de validation',
        'Priorité',
        'Latitude',
        'Longitude',
        'Photo URL',
        'Demande suppression',
        'Date demande suppression',
        'Nombre de commentaires'
    ];

    // Début du CSV avec BOM UTF-8 pour Excel
    $csvData = "\xEF\xBB\xBF" . implode(',', array_map(function($header) {
          return '"' . str_replace('"', '""', $header) . '"';
        }, $headers)) . "\n";

    // Données
    foreach ($signalements as $signalement) {
      $row = [
          $signalement->getId(),
          $this->escapeCsvField($signalement->getTitre()),
          $this->escapeCsvField($this->truncateText($signalement->getDescription(), 200)),
          $signalement->getDateSignalement()->format('Y-m-d H:i:s'),
          $signalement->getVille() ? $this->escapeCsvField($signalement->getVille()->getNom()) : 'N/A',
          $signalement->getArrondissement() ? $this->escapeCsvField($signalement->getArrondissement()->getNom()) : 'N/A',
          $signalement->getCategorie() ? $this->escapeCsvField($signalement->getCategorie()->getNom()) : 'N/A',
          $this->escapeCsvField($signalement->getUtilisateur()->getEmail()),
          $this->escapeCsvField($signalement->getUtilisateur()->getNom()),
          $this->escapeCsvField($signalement->getUtilisateur()->getPrenom()),
          $this->escapeCsvField($this->getStatutLabel($signalement->getStatut()->value)),
          $this->escapeCsvField($this->getEtatValidationLabel($signalement->getEtatValidation()->value)),
          $signalement->getPriorite() ?? 1,
          $signalement->getLatitude() ?? 'N/A',
          $signalement->getLongitude() ?? 'N/A',
          $this->escapeCsvField($signalement->getPhotoUrl() ?? 'N/A'),
          $this->escapeCsvField($signalement->getDemandeSuppressionStatut() ?? 'Aucune'),
          $signalement->getDateDemandeSuppressionStatut() ?
              $signalement->getDateDemandeSuppressionStatut()->format('Y-m-d H:i:s') : 'N/A',
          count($signalement->getCommentaires())
      ];

      $csvData .= implode(',', $row) . "\n";
    }

    // Statistiques en fin de fichier
    $csvData .= "\n\n\"=== STATISTIQUES ===\"\n";
    $csvData .= "\"Total signalements:\"," . count($signalements) . "\n";
    $csvData .= "\"Date d'export:\"," . date('Y-m-d H:i:s') . "\n";

    // Créer la réponse
    $response = new Response($csvData);
    $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
    $response->headers->set('Content-Disposition',
        $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename . '.csv')
    );

    return $response;
  }

  /**
   * Génère un export Excel (si PhpSpreadsheet est installé)
   */
  private function generateExcelExport(array $signalements, string $filename): Response
  {
    // Note: Nécessite PhpSpreadsheet
    // composer require phpoffice/phpspreadsheet

    if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
      // Fallback vers CSV si PhpSpreadsheet n'est pas installé
      $this->addFlash('info', 'Export Excel non disponible, utilisation du format CSV.');
      return $this->generateCsvExport($signalements, $filename);
    }

    // Code pour générer l'Excel (à implémenter si nécessaire)
    return $this->generateCsvExport($signalements, $filename);
  }

  /**
   * Échappe les champs CSV
   */
  private function escapeCsvField(?string $field): string
  {
    if ($field === null) {
      return '""';
    }

    $field = str_replace('"', '""', $field);
    $field = str_replace(["\r", "\n"], ' ', $field);

    return '"' . $field . '"';
  }

  /**
   * Tronque un texte
   */
  private function truncateText(?string $text, int $maxLength): string
  {
    if ($text === null) {
      return '';
    }

    if (strlen($text) <= $maxLength) {
      return $text;
    }

    return substr($text, 0, $maxLength) . '...';
  }

  /**
   * Retourne le label du statut
   */
  private function getStatutLabel(string $statut): string
  {
    return match($statut) {
      'nouveau' => 'Nouveau',
      'en_cours' => 'En cours',
      'resolu' => 'Résolu',
      'ferme' => 'Fermé',
      default => ucfirst($statut)
    };
  }



  /**
   * Retourne le label de l'état de validation
   */
  private function getEtatValidationLabel(string $etat): string
  {
    return match($etat) {
      'en_attente' => 'En attente',
      'valide' => 'Validé',
      'rejete' => 'Rejeté',
      default => ucfirst($etat)
    };
  }


  /**
   * Compte les modérateurs
   */
  private function countModerators(array $utilisateurs): int
  {
    return count(array_filter($utilisateurs, function($user) {
      return in_array('ROLE_MODERATOR', $user->getRoles());
    }));
  }

  /**
   * Compte les administrateurs
   */
  private function countAdmins(array $utilisateurs): int
  {
    return count(array_filter($utilisateurs, function($user) {
      return in_array('ROLE_ADMIN', $user->getRoles());
    }));
  }

  /**
   * Compte les utilisateurs simples
   */
  private function countUsers(array $utilisateurs): int
  {
    return count(array_filter($utilisateurs, function($user) {
      $roles = $user->getRoles();
      return !in_array('ROLE_ADMIN', $roles) && !in_array('ROLE_MODERATOR', $roles);
    }));
  }


  /**
   * Signalements par mois
   */

  private function getSignalementsParMois(EntityManagerInterface $entityManager): array
  {
    // Récupération des signalements des 12 derniers mois
    $dateLimit = new \DateTime('-12 months');

    $signalements = $entityManager->getRepository(Signalement::class)
        ->createQueryBuilder('s')
        ->where('s.dateSignalement >= :dateLimit')
        ->setParameter('dateLimit', $dateLimit)
        ->getQuery()
        ->getResult();

    // Grouper par mois en PHP
    $groupedData = [];
    foreach ($signalements as $signalement) {
      $dateSignalement = $signalement->getDateSignalement();
      if ($dateSignalement) {
        $periode = $dateSignalement->format('Y-m');
        if (!isset($groupedData[$periode])) {
          $groupedData[$periode] = 0;
        }
        $groupedData[$periode]++;
      }
    }

    // Convertir en format attendu par le JavaScript du template
    $data = [];
    foreach ($groupedData as $periode => $count) {
      $dateParts = explode('-', $periode);
      $annee = $dateParts[0];
      $moisNum = $dateParts[1];

      // Convertir le numéro de mois en nom de mois français
      $nomseMois = [
          '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',
          '04' => 'Avril', '05' => 'Mai', '06' => 'Juin',
          '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre',
          '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
      ];

      $data[$periode] = [
          'mois' => $nomseMois[$moisNum] ?? $moisNum,
          'annee' => $annee,
          'total' => $count
      ];
    }

    // Trier par période décroissante
    krsort($data);

    return $data;
  }



  /**
   * Signalements par statut
   */
  private function getSignalementsParStatut(EntityManagerInterface $entityManager): array
  {
    $signalements = $entityManager->getRepository(Signalement::class)->findAll();

    $data = [];
    foreach ($signalements as $signalement) {
      $statut = $signalement->getStatut()->value;

      if (!isset($data[$statut])) {
        $data[$statut] = [
            'statut' => $this->getStatutLabel($statut),
            'total' => 0
        ];
      }
      $data[$statut]['total']++;
    }

    // Trier par total décroissant
    uasort($data, function($a, $b) {
      return $b['total'] <=> $a['total'];
    });

    return array_values($data);
  }

  /**
   * Signalements par ville
   */

  private function getSignalementsParVille(EntityManagerInterface $entityManager): array
  {
    $signalements = $entityManager->getRepository(Signalement::class)
        ->createQueryBuilder('s')
        ->leftJoin('s.ville', 'v')
        ->addSelect('v')
        ->getQuery()
        ->getResult();

    $data = [];
    foreach ($signalements as $signalement) {
      $ville = $signalement->getVille() ? $signalement->getVille()->getNom() : 'Non spécifiée';

      if (!isset($data[$ville])) {
        $data[$ville] = [
            'ville' => $ville,
            'total' => 0
        ];
      }
      $data[$ville]['total']++;
    }

    // Trier par total décroissant et limiter à 10
    uasort($data, function($a, $b) {
      return $b['total'] <=> $a['total'];
    });

    return array_slice(array_values($data), 0, 10);
  }


  #[Route('/utilisateurs/{id}/toggle-status', name: 'app_admin_toggle_user_status', methods: ['POST'])]
  public function toggleUserStatus(
      int                    $id,
      UtilisateurRepository  $utilisateurRepository,
      EntityManagerInterface $entityManager,
      Request                $request
  ): Response
  {
    $utilisateur = $utilisateurRepository->find($id);

    if (!$utilisateur) {
      $this->addFlash('error', 'Utilisateur introuvable.');
      return $this->redirectToRoute('app_admin_utilisateurs');
    }

    if (!$this->isCsrfTokenValid('toggle_user_status_' . $utilisateur->getId(), $request->request->get('_token'))) {
      $this->addFlash('error', 'Token de sécurité invalide.');
      return $this->redirectToRoute('app_admin_utilisateurs');
    }

    try {
      $utilisateur->setEstValide(!$utilisateur->isEstValide());
      $entityManager->flush();

      $status = $utilisateur->isEstValide() ? 'validé' : 'désactivé';
      $this->addFlash('success', "Le compte de {$utilisateur->getPrenom()} {$utilisateur->getNom()} a été {$status}.");
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue lors de la modification du statut.');
    }

    return $this->redirectToRoute('app_admin_utilisateurs');
  }
  // Ajoutez cette méthode dans la classe AdminController


  #[Route('/demandes-suppression', name: 'app_admin_demandes_suppression')]
  public function demandesSuppressionIndex(
      SignalementRepository $signalementRepository,
      Request $request
  ): Response {
    $statut = $request->query->get('statut', '');
    $search = $request->query->get('search', '');

    $qb = $signalementRepository->createQueryBuilder('s')
        ->leftJoin('s.utilisateur', 'u')
        ->leftJoin('s.ville', 'v')
        ->leftJoin('s.arrondissement', 'a')
        ->leftJoin('s.categorie', 'c')
        ->leftJoin('s.moderateurSuppressionStatut', 'm')
        ->where('s.demandeSuppressionStatut IS NOT NULL')
        ->addSelect('u', 'v', 'a', 'c', 'm');

    if ($statut) {
      $qb->andWhere('s.demandeSuppressionStatut = :statut')
          ->setParameter('statut', $statut);
    }

    if ($search) {
      $qb->andWhere('s.titre LIKE :search OR u.nom LIKE :search OR u.prenom LIKE :search')
          ->setParameter('search', '%' . $search . '%');
    }

    $demandes = $qb->orderBy('s.dateDemandeSuppressionStatut', 'DESC')
        ->getQuery()
        ->getResult();

    // Statistiques
    $stats = [
        'total' => count($demandes),
        'en_attente' => count(array_filter($demandes, fn($s) => $s->getDemandeSuppressionStatut() === 'en_attente')),
        'acceptees' => count(array_filter($demandes, fn($s) => $s->getDemandeSuppressionStatut() === 'acceptee')),
        'refusees' => count(array_filter($demandes, fn($s) => $s->getDemandeSuppressionStatut() === 'refusee')),
    ];

    return $this->render('admin/demandes_suppression.html.twig', [
        'demandes' => $demandes,
        'stats' => $stats,
        'current_filters' => [
            'statut' => $statut,
            'search' => $search,
        ],
    ]);
  }

  #[Route('/demandes-suppression/{id}/traiter', name: 'app_admin_traiter_demande_suppression', methods: ['POST'])]
  public function traiterDemandeSuppressionAction(
      int $id,
      Request $request,
      SignalementRepository $signalementRepository,
      EntityManagerInterface $entityManager,
      NotificationService $notificationService
  ): Response {
    $signalement = $signalementRepository->find($id);

    if (!$signalement) {
      throw $this->createNotFoundException('Signalement introuvable.');
    }

    $action = $request->request->get('action');
    $commentaire = $request->request->get('commentaire', '');

    if ($action === 'accepter') {
      $signalement->setDemandeSuppressionStatut('acceptee');
      $signalement->setModerateurSuppressionStatut($this->getUser());
      $signalement->setCommentaireSuppressionStatut($commentaire);

      $this->addFlash('success', 'Demande de suppression acceptée avec succès.');

      // Optionnel : Marquer le signalement comme supprimé
      // $signalement->setStatut(StatutSignalement::SUPPRIME);

    } elseif ($action === 'refuser') {
      if (empty($commentaire)) {
        $this->addFlash('error', 'Un commentaire est requis pour refuser une demande.');
        return $this->redirectToRoute('app_admin_demandes_suppression');
      }

      $signalement->setDemandeSuppressionStatut('refusee');
      $signalement->setModerateurSuppressionStatut($this->getUser());
      $signalement->setCommentaireSuppressionStatut($commentaire);

      $this->addFlash('success', 'Demande de suppression refusée avec motif.');
    }

    $entityManager->flush();

    return $this->redirectToRoute('app_admin_demandes_suppression');
  }

  #[Route('/test-notifications', name: 'app_admin_test_notifications')]
  public function testNotifications(): Response
  {
    if ($this->getParameter('kernel.environment') !== 'dev') {
      throw $this->createNotFoundException();
    }

    $this->addFlash('success', 'Test de notification réussi !');
    return $this->redirectToRoute('app_admin_dashboard');
  }

  private function getSignalementsParCategorie(EntityManagerInterface $entityManager): array
  {
    $signalements = $entityManager->getRepository(Signalement::class)
        ->createQueryBuilder('s')
        ->leftJoin('s.categorie', 'c')
        ->addSelect('c')
        ->getQuery()
        ->getResult();

    $data = [];
    foreach ($signalements as $signalement) {
      $categorie = $signalement->getCategorie() ? $signalement->getCategorie()->getNom() : 'Non spécifiée';

      if (!isset($data[$categorie])) {
        $data[$categorie] = [
            'categorie' => $categorie,
            'total' => 0
        ];
      }
      $data[$categorie]['total']++;
    }

    // Trier par total décroissant et limiter à 10
    uasort($data, function($a, $b) {
      return $b['total'] <=> $a['total'];
    });

    return array_slice(array_values($data), 0, 10);
  }


  private function getSignalementsRecents(EntityManagerInterface $entityManager, int $limit = 5): array
  {
    return $entityManager->getRepository(Signalement::class)
        ->createQueryBuilder('s')
        ->leftJoin('s.utilisateur', 'u')
        ->leftJoin('s.ville', 'v')
        ->leftJoin('s.categorie', 'c')
        ->addSelect('u', 'v', 'c')
        ->orderBy('s.dateSignalement', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
  }

  private function getSignalementsParEtatValidation(EntityManagerInterface $entityManager): array
  {
    $signalements = $entityManager->getRepository(Signalement::class)->findAll();

    $data = [];
    foreach ($signalements as $signalement) {
      $etat = $signalement->getEtatValidation()->value;

      if (!isset($data[$etat])) {
        $data[$etat] = [
            'etat' => $this->getEtatValidationLabel($etat),
            'total' => 0
        ];
      }
      $data[$etat]['total']++;
    }

    return array_values($data);
  }




  private function getEvolutionMensuelle(EntityManagerInterface $entityManager, string $type): array
  {
    if ($type === 'signalements') {
      return $this->getSignalementsParMois($entityManager);
    }

    // Solution simple pour PostgreSQL - récupérer tous les utilisateurs des 12 derniers mois
    $dateLimit = new \DateTime('-12 months');

    $utilisateurs = $entityManager->getRepository(Utilisateur::class)
        ->createQueryBuilder('u')
        ->where('u.dateInscription >= :dateLimit')
        ->setParameter('dateLimit', $dateLimit)
        ->getQuery()
        ->getResult();

    // Grouper par mois en PHP
    $groupedData = [];
    foreach ($utilisateurs as $utilisateur) {
      $dateInscription = $utilisateur->getDateInscription();
      if ($dateInscription) {
        $periode = $dateInscription->format('Y-m');
        if (!isset($groupedData[$periode])) {
          $groupedData[$periode] = 0;
        }
        $groupedData[$periode]++;
      }
    }

    // Convertir en format attendu et trier
    $data = [];
    foreach ($groupedData as $periode => $count) {
      $data[] = [
          'periode' => $periode,
          'count' => $count
      ];
    }

    // Trier par période décroissante
    usort($data, function($a, $b) {
      return strcmp($b['periode'], $a['periode']);
    });

    return $data;
  }



  private function getRecentActivities(SignalementRepository $signalementRepository, UtilisateurRepository $utilisateurRepository): array
  {
    $activities = [];

    // Signalements récents
    $recentSignalements = $signalementRepository->findBy([], ['dateSignalement' => 'DESC'], 5);
    foreach ($recentSignalements as $signalement) {
      $activities[] = [
          'type' => 'signalement',
          'date' => $signalement->getDateSignalement(),
          'message' => "Nouveau signalement : " . $signalement->getTitre(),
          'user' => $signalement->getUtilisateur()->getPrenom() . ' ' . $signalement->getUtilisateur()->getNom(),
          'icon' => 'fas fa-exclamation-triangle',
          'color' => 'warning'
      ];
    }

    // Utilisateurs récents
    $recentUsers = $utilisateurRepository->findBy([], ['dateInscription' => 'DESC'], 3);
    foreach ($recentUsers as $user) {
      $activities[] = [
          'type' => 'utilisateur',
          'date' => $user->getDateInscription(),
          'message' => "Nouvel utilisateur inscrit",
          'user' => $user->getPrenom() . ' ' . $user->getNom(),
          'icon' => 'fas fa-user-plus',
          'color' => 'success'
      ];
    }

    // Trier par date décroissante
    usort($activities, function($a, $b) {
      return $b['date'] <=> $a['date'];
    });

    return array_slice($activities, 0, 8);
  }


  // ===============================
  // GESTION DES RÔLES UTILISATEURS
  // ===============================

  #[Route('/users/roles', name: 'app_admin_users_roles')]
  public function usersRoles(
      UtilisateurRepository $utilisateurRepository,
      EntityManagerInterface $entityManager,
      Request $request
  ): Response {
    // Traitement des modifications de rôles
    if ($request->isMethod('POST')) {
      $action = $request->request->get('action');

      if ($action === 'change_role') {
        $userId = $request->request->get('user_id');
        $newRole = $request->request->get('role');

        $user = $utilisateurRepository->find($userId);
        if ($user && in_array($newRole, ['ROLE_USER', 'ROLE_MODERATOR', 'ROLE_ADMIN'])) {
          // Vérifier qu'on ne se retire pas ses propres droits d'admin
          if ($user === $this->getUser() && $newRole !== 'ROLE_ADMIN') {
            $this->addFlash('error', 'Vous ne pouvez pas retirer vos propres droits d\'administrateur.');
            return $this->redirectToRoute('app_admin_users_roles');
          }

          $user->setRoles([$newRole]);
          $entityManager->flush();

          $this->addFlash('success',
              "Rôle de {$user->getPrenom()} {$user->getNom()} modifié avec succès."
          );
        } else {
          $this->addFlash('error', 'Utilisateur ou rôle invalide.');
        }
      } elseif ($action === 'bulk_role_change') {
        $userIds = $request->request->get('user_ids', []);
        $newRole = $request->request->get('bulk_role');

        if (!empty($userIds) && in_array($newRole, ['ROLE_USER', 'ROLE_MODERATOR', 'ROLE_ADMIN'])) {
          $count = 0;
          foreach ($userIds as $userId) {
            $user = $utilisateurRepository->find($userId);
            if ($user && $user !== $this->getUser()) { // Ne pas modifier son propre rôle
              $user->setRoles([$newRole]);
              $count++;
            }
          }
          $entityManager->flush();

          $this->addFlash('success', "$count utilisateur(s) modifié(s) avec succès.");
        } else {
          $this->addFlash('error', 'Aucun utilisateur sélectionné ou rôle invalide.');
        }
      }

      return $this->redirectToRoute('app_admin_users_roles');
    }

    // Récupération des utilisateurs avec filtres
    $search = $request->query->get('search', '');
    $roleFilter = $request->query->get('role_filter', '');
    $statusFilter = $request->query->get('status_filter', '');
    $sortBy = $request->query->get('sort', 'date_desc');

    $qb = $utilisateurRepository->createQueryBuilder('u');

    // Filtres
    if (!empty($search)) {
      $qb->andWhere('u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search')
          ->setParameter('search', '%' . $search . '%');
    }

    if (!empty($roleFilter)) {
      $qb->andWhere('u.roles LIKE :role')
          ->setParameter('role', '%"' . $roleFilter . '"%');
    }

    if ($statusFilter === 'active') {
      $qb->andWhere('u.estValide = true');
    } elseif ($statusFilter === 'inactive') {
      $qb->andWhere('u.estValide = false');
    }

    // Tri
    switch ($sortBy) {
      case 'name_asc':
        $qb->orderBy('u.nom', 'ASC')->addOrderBy('u.prenom', 'ASC');
        break;
      case 'name_desc':
        $qb->orderBy('u.nom', 'DESC')->addOrderBy('u.prenom', 'DESC');
        break;
      case 'email_asc':
        $qb->orderBy('u.email', 'ASC');
        break;
      case 'date_asc':
        $qb->orderBy('u.dateInscription', 'ASC');
        break;
      default:
        $qb->orderBy('u.dateInscription', 'DESC');
    }

    $utilisateurs = $qb->getQuery()->getResult();

    // Statistiques par rôle
    $stats = [
        'total' => count($utilisateurs),
        'admins' => $this->countUsersByRole($utilisateurs, 'ROLE_ADMIN'),
        'moderateurs' => $this->countUsersByRole($utilisateurs, 'ROLE_MODERATOR'),
        'citoyens' => $this->countUsersByRole($utilisateurs, 'ROLE_USER'),
        'actifs' => count(array_filter($utilisateurs, fn($u) => $u->isEstValide())),
        'inactifs' => count(array_filter($utilisateurs, fn($u) => !$u->isEstValide())),
    ];

    return $this->render('admin/users/roles.html.twig', [
        'utilisateurs' => $utilisateurs,
        'stats' => $stats,
        'current_filters' => [
            'search' => $search,
            'role_filter' => $roleFilter,
            'status_filter' => $statusFilter,
            'sort' => $sortBy,
        ],
        'roles_disponibles' => [
            'ROLE_USER' => 'Citoyen',
            'ROLE_MODERATOR' => 'Modérateur',
            'ROLE_ADMIN' => 'Administrateur',
        ],
    ]);
  }

  /**
   * Compte le nombre d'utilisateurs ayant un rôle spécifique
   */
  private function countUsersByRole(array $utilisateurs, string $role): int
  {
    return count(array_filter($utilisateurs, function($user) use ($role) {
      return in_array($role, $user->getRoles());
    }));
  }

  /**
   * Obtient le libellé d'un rôle
   */
  private function getRoleLabel(string $role): string
  {
    return match($role) {
      'ROLE_ADMIN' => 'Administrateur',
      'ROLE_MODERATOR' => 'Modérateur',
      'ROLE_USER' => 'Citoyen',
      default => 'Inconnu'
    };
  }

  /**
   * Obtient le rôle principal d'un utilisateur (le plus élevé)
   */
  private function getMainRole(Utilisateur $user): string
  {
    $roles = $user->getRoles();

    if (in_array('ROLE_ADMIN', $roles)) {
      return 'ROLE_ADMIN';
    } elseif (in_array('ROLE_MODERATOR', $roles)) {
      return 'ROLE_MODERATOR';
    } else {
      return 'ROLE_USER';
    }
  }


    // ========================
    // JOURNAL DES VALIDATIONS
    // ========================



  #[Route('/journal/validations', name: 'app_admin_journal_validations')]
  public function journalValidations(
      JournalValidationRepository $journalRepository,
      UtilisateurRepository $utilisateurRepository,
      Request $request
  ): Response
  {
    // ✅ Paramètres de filtrage
    $filters = [
        'search' => $request->query->get('search', ''),
        'action_filter' => $request->query->get('action_filter', ''),
        'moderator_filter' => $request->query->get('moderator_filter', ''),
        'date_from' => null,
        'date_to' => null,
    ];

    // ✅ Gestion sécurisée des dates
    if ($dateFrom = $request->query->get('date_from')) {
      try {
        $filters['date_from'] = new \DateTime($dateFrom . ' 00:00:00');
      } catch (\Exception $e) {
        $this->addFlash('error', 'Format de date de début invalide.');
      }
    }

    if ($dateTo = $request->query->get('date_to')) {
      try {
        $filters['date_to'] = new \DateTime($dateTo . ' 23:59:59');
      } catch (\Exception $e) {
        $this->addFlash('error', 'Format de date de fin invalide.');
      }
    }

    $page = max(1, $request->query->getInt('page', 1));
    $limit = 20;

    // ✅ Utilisation des méthodes Repository
    $journalEntries = $journalRepository->findWithFilters($filters, $page, $limit);
    $totalItems = $journalRepository->countWithFilters($filters);
    $totalPages = max(1, ceil($totalItems / $limit));

    // ✅ Statistiques
    $stats = [
        'total' => $journalRepository->count([]),
        'validated' => $journalRepository->count(['action' => 'validé']),
        'rejected' => $journalRepository->count(['action' => 'rejeté']),
        'pending' => $journalRepository->count(['action' => 'en_attente']),
    ];

    // ✅ SOLUTION SIMPLE qui marche TOUJOURS - Filtrage PHP
    $allUsers = $utilisateurRepository->findAll();
    $moderators = [];

    foreach ($allUsers as $user) {
      $roles = $user->getRoles();
      if (in_array('ROLE_MODERATOR', $roles) || in_array('ROLE_ADMIN', $roles)) {
        $moderators[] = $user;
      }
    }

    // Tri par prénom
    usort($moderators, function($a, $b) {
      return $a->getPrenom() <=> $b->getPrenom();
    });

    // ✅ Pagination
    $pagination = [
        'current' => $page,
        'total' => $totalPages,
        'pageCount' => $totalPages,
        'pagesInRange' => range(max(1, $page - 2), min($totalPages, $page + 2))
    ];

    if ($page > 1) $pagination['previous'] = $page - 1;
    if ($page < $totalPages) $pagination['next'] = $page + 1;

    // ✅ Filtres pour le template
    $currentFilters = [
        'search' => $request->query->get('search', ''),
        'action_filter' => $request->query->get('action_filter', ''),
        'moderator_filter' => $request->query->get('moderator_filter', ''),
        'date_from' => $request->query->get('date_from', ''),
        'date_to' => $request->query->get('date_to', ''),
    ];

    return $this->render('admin/journal/validations.html.twig', [
        'journal_entries' => $journalEntries,
        'stats' => $stats,
        'moderators' => $moderators,
        'current_filters' => $currentFilters,
        'pagination' => $pagination,
        'totalItems' => $totalItems,
    ]);
  }

    #[Route('/journal/export', name: 'app_admin_journal_export')]
    public function exportJournal(
        JournalValidationRepository $journalRepository,
        Request $request
    ): Response
    {
      $format = $request->query->get('format', 'csv');

      // Récupérer toutes les entrées du journal
      $journalEntries = $journalRepository->createQueryBuilder('j')
          ->leftJoin('j.signalement', 's')
          ->leftJoin('j.moderateur', 'm')
          ->leftJoin('s.ville', 'v')
          ->addSelect('s', 'm', 'v')
          ->orderBy('j.dateValidation', 'DESC')
          ->getQuery()
          ->getResult();

      if ($format === 'pdf') {
        // Export en PDF
        $this->addFlash('info', 'Export PDF en cours de développement.');
        return $this->redirectToRoute('app_admin_journal_validations');
      }

      // Export CSV
      $csvData = "Date,Modérateur,Action,Signalement,Ville,Commentaire\n";

      foreach ($journalEntries as $entry) {
        $csvData .= sprintf(
            "%s,%s,%s,%s,%s,%s\n",
            $entry->getDateValidation()->format('Y-m-d H:i:s'),
            $entry->getModerateur()->getPrenom() . ' ' . $entry->getModerateur()->getNom(),
            $entry->getAction(),
            '"' . str_replace('"', '""', $entry->getSignalement()->getTitre()) . '"',
            $entry->getSignalement()->getVille() ? $entry->getSignalement()->getVille()->getNom() : 'N/A',
            '"' . str_replace('"', '""', $entry->getCommentaire() ?? '') . '"'
        );
      }

      $response = new Response($csvData);
      $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
      $response->headers->set('Content-Disposition',
          $response->headers->makeDisposition(
              ResponseHeaderBag::DISPOSITION_ATTACHMENT,
              'journal_validations_' . date('Y-m-d_H-i-s') . '.csv'
          )
      );

      return $response;
    }


  // ========================
  // RAPPORTS ET ANALYSES
  // ========================
  // ✅ CORRECTION: Ajoutez une priorité élevée pour éviter le conflit de routes
  // ✅ CORRECTION: Utiliser JournalValidation pour les dates de validation
  private function calculerStatistiquesSimples(array $signalements, \DateTime $startDate, \DateTime $endDate): array
  {
    $total = count($signalements);
    $nbJours = max(1, $startDate->diff($endDate)->days + 1);

    $valides = 0;
    $rejetes = 0;
    $enAttente = 0;
    $resolus = 0;
    $tempsTraitement = [];

    foreach ($signalements as $signalement) {
      $etatValidation = $signalement->getEtatValidation();
      if ($etatValidation instanceof \App\Enum\EtatValidation) {
        switch ($etatValidation->value) {
          case 'valide':
            $valides++;
            break;
          case 'rejete':
            $rejetes++;
            break;
          case 'en_attente':
            $enAttente++;
            break;
        }
      }

      $statut = $signalement->getStatut();
      if ($statut instanceof \App\Enum\StatutSignalement && $statut->value === 'resolu') {
        $resolus++;
      }

      // ✅ Calcul temps de traitement via JournalValidation
      $dateValidation = $this->getDateValidationFromJournal($signalement);
      if ($dateValidation) {
        $diff = $signalement->getDateSignalement()->diff($dateValidation);
        $tempsTraitement[] = $diff->days;
      }
    }

    $tempsMoyen = !empty($tempsTraitement) ? round(array_sum($tempsTraitement) / count($tempsTraitement), 1) : 0;
    $tauxResolution = $total > 0 ? round(($resolus / $total) * 100, 1) : 0;

    return [
        'total_signalements' => $total,
        'signalements_periode' => $total,
        'moyenne_par_jour' => round($total / $nbJours, 1),
        'temps_moyen_traitement' => $tempsMoyen,
        'taux_resolution' => $tauxResolution,
        'valides' => $valides,
        'rejetes' => $rejetes,
        'en_attente' => $enAttente,
        'resolus' => $resolus,
    ];
  }

  private function calculerIndicateursSimples(array $signalements, \DateTime $startDate, \DateTime $endDate): array
  {
    $total = count($signalements);
    if ($total === 0) {
      return [
          'taux_validation' => 0,
          'delai_moyen_reponse' => 0,
          'satisfaction_estimee' => 0,
          'efficacite_moderation' => 0,
      ];
    }

    $valides = 0;
    $delaisReponse = [];
    $resolus = 0;

    foreach ($signalements as $signalement) {
      $etatValidation = $signalement->getEtatValidation();
      if ($etatValidation instanceof \App\Enum\EtatValidation && $etatValidation->value === 'valide') {
        $valides++;
      }

      $statut = $signalement->getStatut();
      if ($statut instanceof \App\Enum\StatutSignalement && $statut->value === 'resolu') {
        $resolus++;
      }

      // ✅ Utiliser JournalValidation pour la date de validation
      $dateValidation = $this->getDateValidationFromJournal($signalement);
      if ($dateValidation) {
        $delai = $signalement->getDateSignalement()->diff($dateValidation)->days;
        $delaisReponse[] = $delai;
      }
    }

    $tauxValidation = round(($valides / $total) * 100, 1);
    $delaiMoyen = !empty($delaisReponse) ? round(array_sum($delaisReponse) / count($delaisReponse), 1) : 0;
    $satisfactionEstimee = round(($resolus / $total) * 100, 1);
    $efficaciteModeration = $delaiMoyen > 0 ? min(100, round(100 / $delaiMoyen * 2, 1)) : 100;

    return [
        'taux_validation' => $tauxValidation,
        'delai_moyen_reponse' => $delaiMoyen,
        'satisfaction_estimee' => $satisfactionEstimee,
        'efficacite_moderation' => $efficaciteModeration,
    ];
  }

// ✅ Méthode helper pour récupérer la date de validation depuis JournalValidation
  private function getDateValidationFromJournal(\App\Entity\Signalement $signalement): ?\DateTimeInterface
  {
    $journalValidations = $signalement->getJournalValidations();

    // Rechercher la validation la plus récente
    $dateValidation = null;
    foreach ($journalValidations as $journal) {
      $action = $journal->getAction();
      if ($action === 'validé' || $action === 'rejeté') {
        if (!$dateValidation || $journal->getDateValidation() > $dateValidation) {
          $dateValidation = $journal->getDateValidation();
        }
      }
    }

    return $dateValidation;
  }


// ✅ Mise à jour de la méthode principale pour utiliser la version optimisée
  #[Route('/signalements/rapports', name: 'app_admin_signalements_rapports', priority: 10)]
  public function rapportsSignalements(
      SignalementRepository $signalementRepository,
      UtilisateurRepository $utilisateurRepository,
      VilleRepository $villeRepository,
      CategorieRepository $categorieRepository,
      Request $request
  ): Response
  {
    // Récupération des paramètres
    $periode = $request->query->get('periode', '30');
    $dateDebut = $request->query->get('date_debut', '');
    $dateFin = $request->query->get('date_fin', '');
    $villeId = $request->query->get('ville', '');
    $categorieId = $request->query->get('categorie', '');

    // Gestion des dates
    $now = new \DateTime();
    try {
      if ($periode === 'custom' && $dateDebut && $dateFin) {
        $startDate = new \DateTime($dateDebut . ' 00:00:00');
        $endDate = new \DateTime($dateFin . ' 23:59:59');
      } else {
        $jours = is_numeric($periode) ? (int)$periode : 30;
        $startDate = (clone $now)->modify("-{$jours} days");
        $endDate = $now;
      }
    } catch (\Exception $e) {
      $startDate = (clone $now)->modify("-30 days");
      $endDate = $now;
      $this->addFlash('warning', 'Format de date invalide, période par défaut appliquée.');
    }

    // Récupérer les signalements avec les données nécessaires
    $allSignalements = $this->getSignalementsAvecDatesValidation($signalementRepository, $startDate, $endDate, $villeId, $categorieId);

    // ✅ Calculer les données de comparaison avec la période précédente
    $comparaisons = $this->calculerComparaisonsPeriode($signalementRepository, $startDate, $endDate, $villeId, $categorieId);

    // Calculs
    $statsGenerales = $this->calculerStatistiquesSimples($allSignalements, $startDate, $endDate);
    $donneesGraphiques = $this->genererDonneesGraphiques($allSignalements, $startDate, $endDate);
    $topContributeurs = $this->getTopContributeursSimple($allSignalements);
    $indicateurs = $this->calculerIndicateursSimples($allSignalements, $startDate, $endDate);

    // Données pour les filtres
    $villes = $villeRepository->findBy([], ['nom' => 'ASC']);
    $categories = $categorieRepository->findBy([], ['nom' => 'ASC']);

    $nbJours = max(1, $startDate->diff($endDate)->days + 1);

    return $this->render('admin/signalements/rapports.html.twig', [
        'stats_generales' => $statsGenerales,
        'donnees_graphiques' => $donneesGraphiques,
        'top_contributeurs' => $topContributeurs,
        'indicateurs' => $indicateurs,
        'comparaisons' => $comparaisons, // ✅ Ajout de cette variable
        'villes' => $villes,
        'categories' => $categories,
        'filtres_actifs' => [
            'periode' => $periode,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'ville' => $villeId,
            'categorie' => $categorieId,
        ],
        'periode_affichage' => [
            'debut' => $startDate,
            'fin' => $endDate,
            'nb_jours' => $nbJours,
        ],
    ]);
  }

  // ✅ Méthode helper pour calculer l'évolution en pourcentage
  private function calculerEvolution(float $valeurPrecedente, float $valeurActuelle): float
  {
    if ($valeurPrecedente == 0) {
      return $valeurActuelle > 0 ? 100 : 0;
    }

    return round((($valeurActuelle - $valeurPrecedente) / $valeurPrecedente) * 100, 1);
  }

  // ✅ Méthode pour récupérer les signalements avec optimisation
  private function getSignalementsAvecDatesValidation(
      SignalementRepository $repo,
      \DateTime $startDate,
      \DateTime $endDate,
      string $villeId = '',
      string $categorieId = ''
  ): array {
    $qb = $repo->createQueryBuilder('s')
        ->leftJoin('s.ville', 'v')
        ->leftJoin('s.categorie', 'c')
        ->leftJoin('s.utilisateur', 'u')
        ->leftJoin('s.journalValidations', 'jv')
        ->addSelect('v', 'c', 'u', 'jv')
        ->where('s.dateSignalement BETWEEN :start AND :end')
        ->setParameter('start', $startDate)
        ->setParameter('end', $endDate)
        ->orderBy('s.dateSignalement', 'DESC');

    if (!empty($villeId) && is_numeric($villeId) && (int)$villeId > 0) {
      $qb->andWhere('v.id = :ville')
          ->setParameter('ville', (int)$villeId);
    }

    if (!empty($categorieId) && is_numeric($categorieId) && (int)$categorieId > 0) {
      $qb->andWhere('c.id = :categorie')
          ->setParameter('categorie', (int)$categorieId);
    }

    return $qb->getQuery()->getResult();
  }


  // ✅ Nouvelle méthode pour calculer les comparaisons avec la période précédente
  private function calculerComparaisonsPeriode(
      SignalementRepository $repo,
      \DateTime $startDate,
      \DateTime $endDate,
      string $villeId = '',
      string $categorieId = ''
  ): array {
    // Calculer la période précédente de même durée
    $nbJours = $startDate->diff($endDate)->days + 1;
    $previousStartDate = (clone $startDate)->modify("-{$nbJours} days");
    $previousEndDate = (clone $startDate)->modify("-1 day");

    // Récupérer les signalements de la période précédente
    $signalementsPrecedents = $this->getSignalementsAvecDatesValidation($repo, $previousStartDate, $previousEndDate, $villeId, $categorieId);

    // Calculer les statistiques de la période précédente
    $statsPrecedentes = $this->calculerStatistiquesSimples($signalementsPrecedents, $previousStartDate, $previousEndDate);

    // Calculer les évolutions
    $evolutionTotal = $this->calculerEvolution(
        count($signalementsPrecedents),
        $repo->count([]) // Total actuel (approximatif pour cet exemple)
    );

    $evolutionResolution = $this->calculerEvolution(
        $statsPrecedentes['taux_resolution'],
        $statsPrecedentes['taux_resolution'] // Sera recalculé avec les vraies données
    );

    return [
        'periode_precedente' => [
            'debut' => $previousStartDate,
            'fin' => $previousEndDate,
            'nb_jours' => $nbJours
        ],
        'total_evolution' => [
            'precedent' => count($signalementsPrecedents),
            'actuel' => count($signalementsPrecedents), // À ajuster selon vos besoins
            'evolution' => $evolutionTotal
        ],
        'resolution_evolution' => [
            'precedent' => $statsPrecedentes['taux_resolution'],
            'actuel' => $statsPrecedentes['taux_resolution'], // À ajuster
            'evolution' => $evolutionResolution
        ],
        'temps_traitement_evolution' => [
            'precedent' => $statsPrecedentes['temps_moyen_traitement'],
            'actuel' => $statsPrecedentes['temps_moyen_traitement'], // À ajuster
            'evolution' => 0 // Calculer selon vos besoins
        ]
    ];
  }


  // ✅ Mise à jour de genererDonneesGraphiques pour inclure toutes les données nécessaires
  private function genererDonneesGraphiques(array $signalements, \DateTime $startDate, \DateTime $endDate): array
  {
    // Évolution temporelle par jour
    $evolutionData = [];
    $tempsPeriode = (clone $startDate);

    while ($tempsPeriode <= $endDate) {
      $dateStr = $tempsPeriode->format('Y-m-d');
      $count = 0;

      foreach ($signalements as $signalement) {
        if ($signalement->getDateSignalement()->format('Y-m-d') === $dateStr) {
          $count++;
        }
      }

      $evolutionData[] = [
          'date' => $tempsPeriode->format('d/m'),
          'count' => $count
      ];

      $tempsPeriode->modify('+1 day');
    }

    // Répartition par statuts
    $statutsData = [];
    $statutCounts = [];

    foreach ($signalements as $signalement) {
      $statut = $signalement->getStatut() ? $signalement->getStatut()->value : 'inconnu';
      $statutCounts[$statut] = ($statutCounts[$statut] ?? 0) + 1;
    }

    foreach ($statutCounts as $statut => $count) {
      $statutsData[] = ['statut' => $statut, 'count' => $count];
    }

    // Répartition par validation
    $validationData = [];
    $validationCounts = [];

    foreach ($signalements as $signalement) {
      $etat = $signalement->getEtatValidation() ? $signalement->getEtatValidation()->value : 'inconnu';
      $validationCounts[$etat] = ($validationCounts[$etat] ?? 0) + 1;
    }

    foreach ($validationCounts as $etat => $count) {
      $validationData[] = ['etatValidation' => $etat, 'count' => $count];
    }

    // Top catégories
    $categorieData = [];
    $categorieCounts = [];

    foreach ($signalements as $signalement) {
      $categorie = $signalement->getCategorie() ? $signalement->getCategorie()->getNom() : 'Non définie';
      $categorieCounts[$categorie] = ($categorieCounts[$categorie] ?? 0) + 1;
    }

    arsort($categorieCounts);
    foreach (array_slice($categorieCounts, 0, 10, true) as $categorie => $count) {
      $categorieData[] = ['categorie' => $categorie, 'count' => $count];
    }

    // Top villes
    $villeData = [];
    $villeCounts = [];

    foreach ($signalements as $signalement) {
      $ville = $signalement->getVille() ? $signalement->getVille()->getNom() : 'Non définie';
      $villeCounts[$ville] = ($villeCounts[$ville] ?? 0) + 1;
    }

    arsort($villeCounts);
    foreach (array_slice($villeCounts, 0, 10, true) as $ville => $count) {
      $villeData[] = ['ville' => $ville, 'count' => $count];
    }

    return [
        'evolution_temporelle' => $evolutionData,
        'repartition_statuts' => $statutsData,
        'repartition_validation' => $validationData,
        'top_categories' => $categorieData,
        'top_villes' => $villeData,
    ];
  }


  private function getTopContributeursSimple(array $signalements): array
  {
    $contributeurs = [];

    foreach ($signalements as $signalement) {
      if ($signalement->getUtilisateur()) {
        $userId = $signalement->getUtilisateur()->getId();
        $userName = $signalement->getUtilisateur()->getPrenom() . ' ' . $signalement->getUtilisateur()->getNom();

        if (!isset($contributeurs[$userId])) {
          $contributeurs[$userId] = [
              'user' => $signalement->getUtilisateur(),
              'nom' => $userName,
              'total' => 0,
              'valides' => 0,
          ];
        }

        $contributeurs[$userId]['total']++;

        $etatValidation = $signalement->getEtatValidation();
        if ($etatValidation instanceof \App\Enum\EtatValidation && $etatValidation->value === 'valide') {
          $contributeurs[$userId]['valides']++;
        }
      }
    }

    uasort($contributeurs, function($a, $b) {
      return $b['total'] <=> $a['total'];
    });

    return array_slice($contributeurs, 0, 10, true);
  }


  // ============================
  // MÉTHODES AUXILIAIRES POUR LES RAPPORTS
  // ============================

  private function getSignalementsByPeriod(SignalementRepository $repo, \DateTime $debut, \DateTime $fin, $villeId = null, $categorieId = null): int
  {
    $qb = $repo->createQueryBuilder('s')
        ->select('COUNT(s.id)')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin);

    if ($villeId) {
      $qb->andWhere('s.ville = :ville')->setParameter('ville', $villeId);
    }
    if ($categorieId) {
      $qb->andWhere('s.categorie = :categorie')->setParameter('categorie', $categorieId);
    }

    return (int) $qb->getQuery()->getSingleScalarResult();
  }

  private function getEvolutionTemporelle(SignalementRepository $repo, \DateTime $debut, \DateTime $fin, $villeId = null, $categorieId = null): array
  {
    $qb = $repo->createQueryBuilder('s')
        ->select('DATE(s.dateSignalement) as date, COUNT(s.id) as count')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin)
        ->groupBy('DATE(s.dateSignalement)')
        ->orderBy('DATE(s.dateSignalement)', 'ASC');

    if ($villeId) {
      $qb->andWhere('s.ville = :ville')->setParameter('ville', $villeId);
    }
    if ($categorieId) {
      $qb->andWhere('s.categorie = :categorie')->setParameter('categorie', $categorieId);
    }

    return $qb->getQuery()->getResult();
  }

  private function getRepartitionStatuts(SignalementRepository $repo, \DateTime $debut, \DateTime $fin, $villeId = null, $categorieId = null): array
  {
    $qb = $repo->createQueryBuilder('s')
        ->select('s.statut, COUNT(s.id) as count')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin)
        ->groupBy('s.statut');

    if ($villeId) {
      $qb->andWhere('s.ville = :ville')->setParameter('ville', $villeId);
    }
    if ($categorieId) {
      $qb->andWhere('s.categorie = :categorie')->setParameter('categorie', $categorieId);
    }

    return $qb->getQuery()->getResult();
  }

  private function getRepartitionValidation(SignalementRepository $repo, \DateTime $debut, \DateTime $fin, $villeId = null, $categorieId = null): array
  {
    $qb = $repo->createQueryBuilder('s')
        ->select('s.etatValidation, COUNT(s.id) as count')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin)
        ->groupBy('s.etatValidation');

    if ($villeId) {
      $qb->andWhere('s.ville = :ville')->setParameter('ville', $villeId);
    }
    if ($categorieId) {
      $qb->andWhere('s.categorie = :categorie')->setParameter('categorie', $categorieId);
    }

    return $qb->getQuery()->getResult();
  }

  private function getTopCategories(SignalementRepository $repo, \DateTime $debut, \DateTime $fin): array
  {
    $qb = $repo->createQueryBuilder('s')
        ->select('c.nom as nom, COUNT(s.id) as count')
        ->leftJoin('s.categorie', 'c')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin)
        ->groupBy('c.id')
        ->orderBy('count', 'DESC')
        ->setMaxResults(10);

    return $qb->getQuery()->getResult();
  }


  private function getTopVilles(SignalementRepository $repo, \DateTime $debut, \DateTime $fin, $villeId = null, $categorieId = null): array
  {
    $qb = $repo->createQueryBuilder('s')
        ->select('v.nom as nom, COUNT(s.id) as count') // Changer 'ville' en 'nom'
        ->leftJoin('s.ville', 'v')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin)
        ->groupBy('v.id')
        ->orderBy('count', 'DESC')
        ->setMaxResults(10);

    if ($villeId) {
      $qb->andWhere('s.ville = :ville')->setParameter('ville', $villeId);
    }
    if ($categorieId) {
      $qb->andWhere('s.categorie = :categorie')->setParameter('categorie', $categorieId);
    }

    return $qb->getQuery()->getResult();
  }


  private function getActivitesRecentes(SignalementRepository $signalementRepo, JournalValidationRepository $journalRepo): array
  {
    // Récupérer les validations récentes
    $validationsRecentes = $journalRepo->createQueryBuilder('j')
        ->leftJoin('j.signalement', 's')
        ->leftJoin('j.moderateur', 'm')
        ->addSelect('s', 'm')
        ->orderBy('j.dateValidation', 'DESC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult();

    $activites = [];


    foreach ($validationsRecentes as $validation) {
      $signalement = $validation->getSignalement();
      $moderateur = $validation->getModerateur();

      $activites[] = [
          'type' => 'validation',
          'title' => 'Validation de signalement', // Ajouter le title manquant
          'date' => $validation->getDateValidation(),
          'message' => $validation->getCommentaire() ?: 'Aucun commentaire',
          'user' => $moderateur ? $moderateur->getPrenom() . ' ' . $moderateur->getNom() : 'Système',
          'icon' => $this->getIconeParAction($validation->getAction()),
          'color' => $this->getCouleurParAction($validation->getAction())
      ];
    }

    // Ajouter les signalements récents
    $signalementsRecents = $signalementRepo->createQueryBuilder('s')
        ->leftJoin('s.utilisateur', 'u')
        ->addSelect('u')
        ->orderBy('s.dateSignalement', 'DESC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();

    foreach ($signalementsRecents as $signalement) {
      $utilisateur = $signalement->getUtilisateur();

      $activites[] = [
          'type' => 'signalement',
          'title' => 'Nouveau signalement', // Ajouter le title manquant
          'date' => $signalement->getDateSignalement(),
          'message' => $signalement->getTitre(),
          'user' => $utilisateur ? $utilisateur->getPrenom() . ' ' . $utilisateur->getNom() : 'Anonyme',
          'icon' => 'exclamation-triangle',
          'color' => 'warning'
      ];
    }

    // Trier par date décroissante
    usort($activites, function($a, $b) {
      return $b['date'] <=> $a['date'];
    });

    return array_slice($activites, 0, 15);
  }



  private function getIconeParAction(string $action): string
  {
    return match($action) {
      'validé' => 'check-circle',
      'rejeté' => 'times-circle',
      'en attente' => 'clock',
      default => 'info-circle'
    };
  }

  private function getCouleurParAction(string $action): string
  {
    return match($action) {
      'validé' => 'success',
      'rejeté' => 'danger',
      'en attente' => 'warning',
      default => 'info'
    };
  }


  private function getTempssMoyenTraitement(SignalementRepository $repo, \DateTime $debut, \DateTime $fin): ?float
  {
    $qb = $repo->createQueryBuilder('s')
        ->select('AVG(TIMESTAMPDIFF(HOUR, s.dateSignalement, s.dateResolution))')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->andWhere('s.dateResolution IS NOT NULL')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin);

    $result = $qb->getQuery()->getSingleScalarResult();
    return $result ? round($result, 1) : null;
  }

  private function getTauxResolution(SignalementRepository $repo, \DateTime $debut, \DateTime $fin): float
  {
    $total = $this->getSignalementsByPeriod($repo, $debut, $fin);

    if ($total === 0) return 0;

    $resolus = $repo->createQueryBuilder('s')
        ->select('COUNT(s.id)')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->andWhere('s.statut = :statut')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin)
        ->setParameter('statut', 'resolu')
        ->getQuery()
        ->getSingleScalarResult();

    return round(($resolus / $total) * 100, 1);
  }

  // Ajouter les autres méthodes auxiliaires...
  private function getActiviteParJourSemaine(SignalementRepository $repo, \DateTime $debut, \DateTime $fin, $villeId = null, $categorieId = null): array
  {
    // Implémentation pour l'activité par jour de la semaine
    return [];
  }

  private function getAnalyseTempsTraitement(SignalementRepository $repo, \DateTime $debut, \DateTime $fin, $villeId = null, $categorieId = null): array
  {
    // Implémentation pour l'analyse des temps de traitement
    return [];
  }

  private function getPeriodePrecedente(\DateTime $debut, \DateTime $fin): array
  {
    $duree = $debut->diff($fin)->days;
    return [
        'debut' => (clone $debut)->modify("-{$duree} days"),
        'fin' => clone $debut,
    ];
  }

  private function getEvolutionComparative(SignalementRepository $repo, \DateTime $debut1, \DateTime $fin1, \DateTime $debut2, \DateTime $fin2): array
  {
    // Implémentation pour la comparaison entre périodes
    return [];
  }

  private function getEvolutionResolution(SignalementRepository $repo, \DateTime $debut1, \DateTime $fin1, \DateTime $debut2, \DateTime $fin2): array
  {
    // Implémentation pour l'évolution du taux de résolution
    return [];
  }

  private function getTopContributeurs(SignalementRepository $repo, \DateTime $debut, \DateTime $fin, $villeId = null, $categorieId = null): array
  {
    // Implémentation pour les top contributeurs
    return [];
  }

  private function getTauxValidation(SignalementRepository $repo, \DateTime $debut, \DateTime $fin): float
  {
    // Implémentation pour le taux de validation
    return 0.0;
  }

  private function getDelaiMoyenReponse(SignalementRepository $repo, \DateTime $debut, \DateTime $fin): ?float
  {
    // Implémentation pour le délai moyen de réponse
    return null;
  }

  private function getSatisfactionEstimee(SignalementRepository $repo, \DateTime $debut, \DateTime $fin): float
  {
    // Implémentation pour la satisfaction estimée
    return 0.0;
  }

  private function getEfficaciteModeration(SignalementRepository $repo, \DateTime $debut, \DateTime $fin): float
  {
    // Implémentation pour l'efficacité de la modération
    return 0.0;
  }

  // ========================
  // GESTION DES CATÉGORIES
  // ========================

  #[Route('/categories', name: 'app_admin_categories')]
  public function categories(
      CategorieRepository $categorieRepository,
      Request $request
  ): Response
  {
    $search = $request->query->get('search', '');
    $tri = $request->query->get('tri', 'nom_asc');

    $qb = $categorieRepository->createQueryBuilder('c');

    if (!empty($search)) {
      $qb->andWhere('c.nom LIKE :search OR c.description LIKE :search')
          ->setParameter('search', '%' . $search . '%');
    }

    // Tri
    switch ($tri) {
      case 'nom_desc':
        $qb->orderBy('c.nom', 'DESC');
        break;
      case 'date_asc':
        $qb->orderBy('c.id', 'ASC');
        break;
      case 'date_desc':
        $qb->orderBy('c.id', 'DESC');
        break;
      default:
        $qb->orderBy('c.nom', 'ASC');
    }

    $categories = $qb->getQuery()->getResult();

    return $this->render('admin/categories/index.html.twig', [
        'categories' => $categories,
        'current_filters' => [
            'search' => $search,
            'tri' => $tri,
        ],
    ]);
  }



  #[Route('/categories/new', name: 'app_admin_categories_new')]
  public function newCategorie(
      Request $request,
      EntityManagerInterface $entityManager
  ): Response
  {
    $categorie = new Categorie();
    $form = $this->createForm(CategorieType::class, $categorie, [
        'is_edit' => false
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->persist($categorie);
      $entityManager->flush();

      $this->addFlash('success', 'Catégorie créée avec succès.');
      return $this->redirectToRoute('app_admin_categories');
    }

    return $this->render('admin/categories/new.html.twig', [
        'form' => $form->createView(),
    ]);
  }

  #[Route('/categories/{id}/edit', name: 'app_admin_categories_edit')]
  public function editCategorie(
      int $id,
      Request $request,
      CategorieRepository $categorieRepository,
      EntityManagerInterface $entityManager
  ): Response
  {
    $categorie = $categorieRepository->find($id);

    if (!$categorie) {
      throw $this->createNotFoundException('Catégorie introuvable.');
    }

    $form = $this->createForm(CategorieType::class, $categorie, [
        'is_edit' => true
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->flush();

      $this->addFlash('success', 'Catégorie modifiée avec succès.');
      return $this->redirectToRoute('app_admin_categories');
    }

    return $this->render('admin/categories/edit.html.twig', [
        'categorie' => $categorie,
        'form' => $form->createView(),
    ]);
  }

  #[Route('/categories/{id}/delete', name: 'app_admin_categories_delete', methods: ['POST'])]
  public function deleteCategorie(
      int $id,
      CategorieRepository $categorieRepository,
      EntityManagerInterface $entityManager,
      Request $request
  ): Response
  {
    $categorie = $categorieRepository->find($id);

    if (!$categorie) {
      $this->addFlash('error', 'Catégorie introuvable.');
      return $this->redirectToRoute('app_admin_categories');
    }

    if (!$this->isCsrfTokenValid('delete_categorie' . $categorie->getId(), $request->request->get('_token'))) {
      $this->addFlash('error', 'Token de sécurité invalide.');
      return $this->redirectToRoute('app_admin_categories');
    }

    try {
      $nom = $categorie->getNom();
      $entityManager->remove($categorie);
      $entityManager->flush();

      $this->addFlash('success', "La catégorie \"{$nom}\" a été supprimée.");
    } catch (\Exception $e) {
      $this->addFlash('error', 'Impossible de supprimer cette catégorie car elle est utilisée par des signalements.');
    }

    return $this->redirectToRoute('app_admin_categories');
  }


  #[Route('/categories/export', name: 'app_admin_categories_export')]
  public function exportCategories(
      CategorieRepository $categorieRepository,
      Request $request
  ): Response
  {
    $format = $request->query->get('format', 'csv'); // csv ou excel

    $categories = $categorieRepository->createQueryBuilder('c')
        ->leftJoin('c.signalements', 's')
        ->addSelect('COUNT(s.id) as nb_signalements')
        ->groupBy('c.id')
        ->orderBy('c.nom', 'ASC')
        ->getQuery()
        ->getResult();

    if ($format === 'csv') {
      return $this->exportCategoriesToCsv($categories);
    }

    // TODO: Implémenter l'export Excel si nécessaire
    $this->addFlash('info', 'Export Excel en cours de développement.');
    return $this->redirectToRoute('app_admin_categories');
  }

  // ============================
  // MÉTHODES AUXILIAIRES POUR LES CATÉGORIES
  // ============================

  private function getCategoriesActives(CategorieRepository $repo): int
  {
    return $repo->createQueryBuilder('c')
        ->select('COUNT(DISTINCT c.id)')
        ->leftJoin('c.signalements', 's')
        ->where('s.id IS NOT NULL')
        ->getQuery()
        ->getSingleScalarResult();
  }

  private function getCategoriesSansSignalement(CategorieRepository $repo): int
  {
    return $repo->createQueryBuilder('c')
        ->select('COUNT(c.id)')
        ->leftJoin('c.signalements', 's')
        ->where('s.id IS NULL')
        ->getQuery()
        ->getSingleScalarResult();
  }

  private function getMoyenneSignalementsParCategorie(CategorieRepository $repo): float
  {
    $result = $repo->createQueryBuilder('c')
        ->select('AVG(subquery.nb_signalements)')
        ->from('(' .
            $repo->createQueryBuilder('c2')
                ->select('COUNT(s2.id) as nb_signalements')
                ->leftJoin('c2.signalements', 's2')
                ->groupBy('c2.id')
                ->getDQL() .
            ') subquery')
        ->getQuery()
        ->getSingleScalarResult();

    return $result ? round($result, 1) : 0;
  }

  private function exportCategoriesToCsv(array $categories): Response
  {
    $filename = 'categories_' . date('Y-m-d_H-i-s') . '.csv';

    $response = new Response();
    $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
    $response->headers->set('Content-Disposition',
        ResponseHeaderBag::DISPOSITION_ATTACHMENT . '; filename="' . $filename . '"'
    );

    $output = fopen('php://temp', 'r+');

    // BOM UTF-8 pour Excel
    fputs($output, "\xEF\xBB\xBF");

    // En-têtes CSV
    fputcsv($output, [
        'ID',
        'Nom',
        'Description',
        'Icône',
        'Couleur',
        'Nombre de signalements'
    ], ';');

    // Données
    foreach ($categories as $result) {
      $categorie = $result[0]; // L'entité Categorie
      $nbSignalements = $result['nb_signalements'] ?? 0;

      fputcsv($output, [
          $categorie->getId(),
          $categorie->getNom(),
          $categorie->getDescription(),
          $categorie->getIcone(),
          $categorie->getCouleur(),
          $nbSignalements
      ], ';');
    }

    rewind($output);
    $content = stream_get_contents($output);
    fclose($output);

    $response->setContent($content);
    return $response;
  }



  // ========================
  // GESTION DES VILLES
  // ========================

#[Route('/villes', name: 'app_admin_villes')]
public function villes(
    VilleRepository $villeRepository,
    DepartementRepository $departementRepository,
    Request $request
): Response
{
  $search = $request->query->get('search', '');
  $departement = $request->query->get('departement', '');
  $tri = $request->query->get('tri', 'nom_asc');
  $page = max(1, $request->query->getInt('page', 1));
  $itemsPerPage = 12;

  $queryBuilder = $villeRepository->createQueryBuilder('v')
      ->leftJoin('v.departement', 'd')
      ->addSelect('d'); // Ajouter explicitement le département

  if (!empty($search)) {
    $queryBuilder->andWhere('v.nom LIKE :search')
        ->setParameter('search', '%' . $search . '%');
  }

  if (!empty($departement)) {
    $queryBuilder->andWhere('v.departement = :departement')
        ->setParameter('departement', $departement);
  }

  // Tri
  switch ($tri) {
    case 'nom_desc':
      $queryBuilder->orderBy('v.nom', 'DESC');
      break;
    case 'departement_asc':
      $queryBuilder->orderBy('d.nom', 'ASC')
          ->addOrderBy('v.nom', 'ASC'); // Ajouter un ordre secondaire
      break;
    case 'departement_desc':
      $queryBuilder->orderBy('d.nom', 'DESC')
          ->addOrderBy('v.nom', 'ASC'); // Ajouter un ordre secondaire
      break;
    default:
      $queryBuilder->orderBy('v.nom', 'ASC');
  }

  // Ajout de la pagination
  $query = $queryBuilder->getQuery();
  $query->setFirstResult(($page - 1) * $itemsPerPage)
      ->setMaxResults($itemsPerPage);

  $villes = $query->getResult();

  // ✅ CORRECTION : Créer une nouvelle requête pour le comptage sans tri ni pagination
  $totalItems = $villeRepository->createQueryBuilder('v')
      ->select('COUNT(v.id)')
      ->leftJoin('v.departement', 'd');

  // Appliquer les mêmes filtres pour le comptage
  if (!empty($search)) {
    $totalItems->andWhere('v.nom LIKE :search')
        ->setParameter('search', '%' . $search . '%');
  }

  if (!empty($departement)) {
    $totalItems->andWhere('v.departement = :departement')
        ->setParameter('departement', $departement);
  }

  $totalItems = $totalItems->getQuery()->getSingleScalarResult();

  // Créer un objet paginator simple
  $paginator = (object) [
      'items' => $villes,
      'totalItems' => $totalItems,
      'totalPages' => ceil($totalItems / $itemsPerPage),
      'currentPage' => $page,
      'hasNextPage' => $page < ceil($totalItems / $itemsPerPage),
      'hasPreviousPage' => $page > 1,
      'nextPage' => $page + 1,
      'previousPage' => $page - 1,
      'pageRange' => range(max(1, $page - 2), min(ceil($totalItems / $itemsPerPage), $page + 2))
  ];

  $departements = $departementRepository->findAll();

  return $this->render('admin/villes/index.html.twig', [
      'villes' => $villes,
      'paginator' => $paginator,
      'departements' => $departements,
      'current_filters' => [
          'search' => $search,
          'departement' => $departement,
          'tri' => $tri,
      ],
  ]);
}


  #[Route('/villes/new', name: 'app_admin_villes_new', methods: ['GET', 'POST'])]
  public function newVille(
      Request $request,
      EntityManagerInterface $entityManager,
      DepartementRepository $departementRepository
  ): Response
  {
    if ($request->isMethod('POST')) {
      // Traitement du formulaire de création rapide
      $nom = $request->request->get('nom');
      $departementId = $request->request->get('departement');
      $latitude = $request->request->get('latitude');
      $longitude = $request->request->get('longitude');

      if ($nom && $departementId && $latitude && $longitude) {
        $departement = $departementRepository->find($departementId);

        if ($departement) {
          $ville = new \App\Entity\Ville();
          $ville->setNom($nom);
          $ville->setDepartement($departement);
          $ville->setLatitudeCentre((float)$latitude);
          $ville->setLongitudeCentre((float)$longitude);

          $entityManager->persist($ville);
          $entityManager->flush();

          $this->addFlash('success', "La ville \"{$nom}\" a été créée avec succès.");
          return $this->redirectToRoute('app_admin_villes');
        } else {
          $this->addFlash('error', 'Département invalide.');
        }
      } else {
        $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
      }
    }

    // Pour le formulaire Symfony
    $ville = new \App\Entity\Ville();
    $form = $this->createForm(VilleType::class, $ville);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->persist($ville);
      $entityManager->flush();

      $this->addFlash('success', 'Ville créée avec succès.');
      return $this->redirectToRoute('app_admin_villes');
    }

    $departements = $departementRepository->findAll();

    return $this->render('admin/villes/form.html.twig', [
        'ville' => $ville,
        'form' => $form->createView(),
        'departements' => $departements,
    ]);
  }

  #[Route('/villes/{id}/edit', name: 'app_admin_villes_edit')]
  public function editVille(
      int $id,
      Request $request,
      VilleRepository $villeRepository,
      EntityManagerInterface $entityManager
  ): Response
  {
    $ville = $villeRepository->find($id);

    if (!$ville) {
      throw $this->createNotFoundException('Ville introuvable.');
    }

    $form = $this->createForm(VilleType::class, $ville);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->flush();

      $this->addFlash('success', 'Ville modifiée avec succès.');
      return $this->redirectToRoute('app_admin_villes');
    }

    return $this->render('admin/villes/form.html.twig', [
        'ville' => $ville,
        'form' => $form->createView(),
    ]);
  }

  #[Route('/villes/{id}/delete', name: 'app_admin_villes_delete', methods: ['POST'])]
  public function deleteVille(
      int $id,
      Request $request,
      VilleRepository $villeRepository,
      EntityManagerInterface $entityManager
  ): Response
  {
    $ville = $villeRepository->find($id);

    if (!$ville) {
      $this->addFlash('error', 'Ville introuvable.');
      return $this->redirectToRoute('app_admin_villes');
    }

    if (!$this->isCsrfTokenValid('delete' . $ville->getId(), $request->request->get('_token'))) {
      $this->addFlash('error', 'Token de sécurité invalide.');
      return $this->redirectToRoute('app_admin_villes');
    }

    try {
      // Vérifier s'il y a des signalements liés
      if (count($ville->getSignalements()) > 0) {
        $this->addFlash('error', 'Impossible de supprimer cette ville car elle contient des signalements.');
        return $this->redirectToRoute('app_admin_villes');
      }

      $entityManager->remove($ville);
      $entityManager->flush();

      $this->addFlash('success', "La ville \"{$ville->getNom()}\" a été supprimée avec succès.");
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue lors de la suppression.');
    }

    return $this->redirectToRoute('app_admin_villes');
  }


#[Route('/villes/export', name: 'app_admin_villes_export')]
public function exportVilles(
    VilleRepository $villeRepository,
    Request $request
): Response
{
  $format = $request->query->get('format', 'csv'); // csv ou excel

  // ✅ CORRECTION : Ajouter toutes les colonnes non-agrégées dans GROUP BY
    // Utilisez :
  $villes = $villeRepository->createQueryBuilder('v')
      ->leftJoin('v.departement', 'd')
      ->leftJoin('v.signalements', 's')
      ->addSelect('COUNT(s.id) as nb_signalements')
      ->addSelect('d.nom as departement_nom')
      ->addSelect('d.id as departement_id') // Ajouter l'ID du département
      ->groupBy('v.id, v.nom, d.id, d.nom') // ✅ Inclure toutes les colonnes sélectionnées
      ->orderBy('v.nom', 'ASC')
      ->getQuery()
      ->getResult();


  if ($format === 'csv') {
    return $this->exportVillesToCsv($villes);
  }

  //Implémentation de l'export
  $this->addFlash('info', 'Export Excel en cours de développement.');
  return $this->redirectToRoute('app_admin_villes');
}

  #[Route('/villes/import', name: 'app_admin_villes_import')]
  public function importVilles(
      Request $request,
      EntityManagerInterface $entityManager
  ): Response
  {
    if ($request->isMethod('POST') && $request->files->has('csv_file')) {
      $file = $request->files->get('csv_file');

      if ($file && $file->getClientOriginalExtension() === 'csv') {
        $imported = $this->processCsvImport($file, $entityManager);

        $this->addFlash('success',
            "Import terminé. {$imported['success']} villes importées, {$imported['errors']} erreurs."
        );
      } else {
        $this->addFlash('error', 'Veuillez sélectionner un fichier CSV valide.');
      }

      return $this->redirectToRoute('app_admin_villes');
    }

    return $this->render('admin/villes/import.html.twig');
  }

  // ============================
  // MÉTHODES AUXILIAIRES POUR LES VILLES
  // ============================

  private function handleQuickVilleCreation(
      Request $request,
      EntityManagerInterface $entityManager,
      DepartementRepository $departementRepository
  ): Response
  {
    $nom = trim($request->request->get('nom'));
    $departementNom = trim($request->request->get('departement', ''));
    $latitude = $request->request->get('latitude');
    $longitude = $request->request->get('longitude');

    if (empty($nom) || empty($latitude) || empty($longitude)) {
      $this->addFlash('error', 'Le nom, la latitude et la longitude sont obligatoires.');
      return $this->redirectToRoute('app_admin_villes');
    }

    try {
      $ville = new Ville();
      $ville->setNom($nom);
      $ville->setSlug($this->generateSlug($nom));
      $ville->setLatitudeCentre((float) $latitude);
      $ville->setLongitudeCentre((float) $longitude);

      // Chercher ou créer le département
      if (!empty($departementNom)) {
        $departement = $departementRepository->findOneBy(['nom' => $departementNom]);
        if (!$departement) {
          $departement = new Departement();
          $departement->setNom($departementNom);
          $departement->setCode(substr(strtoupper($departementNom), 0, 3));
          $entityManager->persist($departement);
        }
        $ville->setDepartement($departement);
      }

      $entityManager->persist($ville);
      $entityManager->flush();

      $this->addFlash('success', "La ville '{$nom}' a été créée avec succès.");
    } catch (\Exception $e) {
      $this->addFlash('error', 'Erreur lors de la création de la ville : ' . $e->getMessage());
    }

    return $this->redirectToRoute('app_admin_villes');
  }

  private function getVillesActives(VilleRepository $repo): int
  {
    return $repo->createQueryBuilder('v')
        ->select('COUNT(DISTINCT v.id)')
        ->leftJoin('v.signalements', 's')
        ->where('s.id IS NOT NULL')
        ->getQuery()
        ->getSingleScalarResult();
  }

  private function getVillesSansSignalement(VilleRepository $repo): int
  {
    return $repo->createQueryBuilder('v')
        ->select('COUNT(v.id)')
        ->leftJoin('v.signalements', 's')
        ->where('s.id IS NULL')
        ->getQuery()
        ->getSingleScalarResult();
  }


  private function getMoyenneSignalementsParVille(VilleRepository $repo): float
  {
    $conn = $repo->getEntityManager()->getConnection();

    $sql = "
        SELECT AVG(nb) as moyenne
        FROM (
            SELECT COUNT(s.id) as nb
            FROM ville v
            LEFT JOIN signalement s ON s.ville_id = v.id
            GROUP BY v.id
        ) as sous_requete
    ";

    $stmt = $conn->prepare($sql);
    $result = $stmt->executeQuery()->fetchOne();

    return $result ? round($result, 1) : 0;
  }

  private function generateSlug(string $text): string
  {
    // Convertir en minuscules et remplacer les accents
    $text = strtolower($text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

    // Remplacer les caractères non alphanumériques par des tirets
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    // Supprimer les tirets en début/fin
    return trim($text, '-');
  }

  private function exportVillesToCsv(array $villes): Response
  {
    $filename = 'villes_' . date('Y-m-d_H-i-s') . '.csv';

    $response = new Response();
    $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
    $response->headers->set('Content-Disposition',
        ResponseHeaderBag::DISPOSITION_ATTACHMENT . '; filename="' . $filename . '"'
    );

    $output = fopen('php://temp', 'r+');

    // BOM UTF-8 pour Excel
    fputs($output, "\xEF\xBB\xBF");

    // En-têtes CSV
    fputcsv($output, [
        'ID',
        'Nom',
        'Slug',
        'Département',
        'Latitude',
        'Longitude',
        'Nombre de signalements'
    ], ';');

    // Données
    foreach ($villes as $result) {
      $ville = $result[0]; // L'entité Ville
      $nbSignalements = $result['nb_signalements'] ?? 0;
      $departementNom = $result['departement_nom'] ?? '';

      fputcsv($output, [
          $ville->getId(),
          $ville->getNom(),
          $ville->getSlug(),
          $departementNom,
          $ville->getLatitudeCentre(),
          $ville->getLongitudeCentre(),
          $nbSignalements
      ], ';');
    }

    rewind($output);
    $content = stream_get_contents($output);
    fclose($output);

    $response->setContent($content);
    return $response;
  }

  private function processCsvImport($file, EntityManagerInterface $entityManager): array
  {
    $imported = ['success' => 0, 'errors' => 0];

    $handle = fopen($file->getPathname(), 'r');
    $header = fgetcsv($handle, 0, ';'); // Lire l'en-tête

    while (($data = fgetcsv($handle, 0, ';')) !== false) {
      try {
        if (count($data) >= 4) { // nom, latitude, longitude minimum
          $ville = new Ville();
          $ville->setNom($data[0]);
          $ville->setSlug($this->generateSlug($data[0]));
          $ville->setLatitudeCentre((float) $data[1]);
          $ville->setLongitudeCentre((float) $data[2]);

          $entityManager->persist($ville);
          $imported['success']++;
        }
      } catch (\Exception $e) {
        $imported['errors']++;
      }
    }

    fclose($handle);
    $entityManager->flush();

    return $imported;
  }

  // ========================
  // PARAMÈTRES SYSTÈME
  // ========================

  #[Route('/parametres', name: 'app_admin_parametres')]
  public function parametres(
      Request $request,
      EntityManagerInterface $entityManager,
      ParameterBagInterface $params
  ): Response
  {
    // Traitement du formulaire de sauvegarde
    if ($request->isMethod('POST')) {
      return $this->handleParametersUpdate($request, $entityManager);
    }

    // Récupération des paramètres actuels
    $parametres = $this->getCurrentParameters($entityManager);

    // Informations système
    $systemInfo = $this->getSystemInfo();

    // Statistiques générales pour le tableau de bord
    $stats = $this->getGeneralStats($entityManager);

    return $this->render('admin/parametres/index.html.twig', [
        'parametres' => $parametres,
        'system_info' => $systemInfo,
        'stats' => $stats,
    ]);
  }


  #[Route('/parametres/backup', name: 'app_admin_parametres_backup', methods: ['POST'])]
  public function createBackup(
      EntityManagerInterface $entityManager,
      Request $request
  ): Response
  {
    // ✅ Vérification du token CSRF
    if (!$this->isCsrfTokenValid('backup_create', $request->request->get('_token'))) {
      $this->addFlash('error', 'Token de sécurité invalide.');
      return $this->redirectToRoute('app_admin_parametres');
    }

    try {
      // ✅ Utiliser un répertoire sécurisé (HORS du public)
      $backupPath = $this->getParameter('kernel.project_dir') . '/var/backups/';

      // ✅ Correction de la condition race avec mkdir
      if (!is_dir($backupPath) && !mkdir($backupPath, 0755, true) && !is_dir($backupPath)) {
        throw new \RuntimeException("Impossible de créer le répertoire de sauvegarde : $backupPath");
      }

      // ✅ Vérifier les permissions d'écriture
      if (!is_writable($backupPath)) {
        throw new \RuntimeException("Le répertoire de sauvegarde n'est pas accessible en écriture : $backupPath");
      }

      // Générer un nom de fichier sécurisé
      $filename = 'backup_' . date('Y-m-d_H-i-s') . '_' . uniqid('', true) . '.sql';
      $fullPath = $backupPath . $filename;

      // ✅ Extraire les infos de connexion de manière sécurisée
      $dbUrl = $_ENV['DATABASE_URL'] ?? '';
      if (empty($dbUrl)) {
        throw new \RuntimeException('DATABASE_URL non configurée');
      }

      $urlParts = parse_url($dbUrl);
      if (!$urlParts) {
        throw new \RuntimeException('Format DATABASE_URL invalide');
      }

      $host = $urlParts['host'] ?? 'localhost';
      $port = $urlParts['port'] ?? 5432;
      $user = $urlParts['user'] ?? '';
      $password = $urlParts['pass'] ?? '';
      $dbName = ltrim($urlParts['path'] ?? '', '/');

      if (empty($dbName)) {
        throw new \RuntimeException('Nom de base de données non trouvé dans DATABASE_URL');
      }

      // ✅ Vérifier que pg_dump est disponible
      $pgDumpPath = $this->findPgDumpExecutable();
      if (!$pgDumpPath) {
        throw new \RuntimeException('pg_dump non trouvé sur le système');
      }

      // ✅ Créer un fichier temporaire pour le mot de passe (plus sécurisé)
      $pgpassFile = tempnam(sys_get_temp_dir(), 'pgpass_');
      if ($pgpassFile === false) {
        throw new \RuntimeException('Impossible de créer le fichier temporaire');
      }

      // Format: hostname:port:database:username:password
      $pgpassContent = sprintf("%s:%d:%s:%s:%s\n", $host, $port, $dbName, $user, $password);
      file_put_contents($pgpassFile, $pgpassContent);
      chmod($pgpassFile, 0600); // Lecture seule pour le propriétaire

      // ✅ Commande PostgreSQL sécurisée
      $command = sprintf(
          'PGPASSFILE=%s %s -h %s -p %d -U %s -F c -b -v -f %s %s 2>&1',
          escapeshellarg($pgpassFile),
          escapeshellarg($pgDumpPath),
          escapeshellarg($host),
          $port,
          escapeshellarg($user),
          escapeshellarg($fullPath),
          escapeshellarg($dbName)
      );

      // ✅ Exécuter avec timeout et gestion d'erreurs
      $output = [];
      $returnCode = 0;

      // Définir un timeout de 5 minutes
      $descriptors = [
          0 => ['pipe', 'r'], // stdin
          1 => ['pipe', 'w'], // stdout
          2 => ['pipe', 'w']  // stderr
      ];

      $process = proc_open($command, $descriptors, $pipes);

      if (!is_resource($process)) {
        throw new \RuntimeException('Impossible de démarrer le processus pg_dump');
      }

      // Fermer stdin
      fclose($pipes[0]);

      // Lire stdout et stderr
      $stdout = stream_get_contents($pipes[1]);
      $stderr = stream_get_contents($pipes[2]);

      fclose($pipes[1]);
      fclose($pipes[2]);

      $returnCode = proc_close($process);

      // ✅ Nettoyer le fichier temporaire
      unlink($pgpassFile);

      // ✅ Vérifier le résultat
      if ($returnCode === 0 && file_exists($fullPath) && filesize($fullPath) > 0) {
        // ✅ UTILISER LA MÉTHODE EXISTANTE formatBytes()
        $fileSize = $this->formatBytes(filesize($fullPath));
        $this->addFlash('success',
            "✅ Sauvegarde PostgreSQL créée avec succès !\n" .
            "📁 Fichier : {$filename}\n" .
            "📊 Taille : {$fileSize}"
        );

        // ✅ Log de l'action pour audit
        $this->logBackupAction($filename, $fileSize, true);

      } else {
        // ✅ Nettoyer le fichier en cas d'échec
        if (file_exists($fullPath)) {
          unlink($fullPath);
        }

        $errorMsg = 'Erreur lors de la création de la sauvegarde PostgreSQL.';
        if (!empty($stderr)) {
          $errorMsg .= "\nDétails : " . $stderr;
        }

        $this->addFlash('error', $errorMsg);
        $this->logBackupAction($filename, '0 B', false, $stderr);
      }

    } catch (\Exception $e) {
      $this->addFlash('error', 'Erreur technique : ' . $e->getMessage());

      // ✅ Nettoyer en cas d'exception
      if (isset($pgpassFile) && file_exists($pgpassFile)) {
        unlink($pgpassFile);
      }
      if (isset($fullPath) && file_exists($fullPath)) {
        unlink($fullPath);
      }
    }

    return $this->redirectToRoute('app_admin_parametres');
  }

// ✅ Méthodes utilitaires (SANS formatBytes qui existe déjà)

  private function findPgDumpExecutable(): ?string
  {
    // Chemins possibles pour pg_dump
    $possiblePaths = [
        '/usr/bin/pg_dump',
        '/usr/local/bin/pg_dump',
        '/opt/homebrew/bin/pg_dump', // macOS Homebrew
        'pg_dump' // Dans le PATH
    ];

    foreach ($possiblePaths as $path) {
      if ($path === 'pg_dump') {
        // Vérifier si dans le PATH
        exec('which pg_dump 2>/dev/null', $output, $returnCode);
        if ($returnCode === 0 && !empty($output)) {
          return trim($output[0]);
        }
      } elseif (is_executable($path)) {
        return $path;
      }
    }

    return null;
  }

  private function logBackupAction(string $filename, string $size, bool $success, string $error = ''): void
  {
    // ✅ Logger l'action pour audit (optionnel)
    $logMessage = sprintf(
        '[BACKUP] %s - Fichier: %s, Taille: %s, Utilisateur: %s',
        $success ? 'SUCCESS' : 'FAILED',
        $filename,
        $size,
        $this->getUser()?->getEmail() ?? 'inconnu'
    );

    if (!$success && $error) {
      $logMessage .= " - Erreur: $error";
    }

    // Ici vous pouvez utiliser le logger Symfony
    // $this->logger->info($logMessage);
  }


  #[Route('/parametres/cache/clear', name: 'app_admin_parametres_cache_clear', methods: ['POST'])]
  public function clearCache(): Response
  {
    try {
      // Vider le cache Symfony
      $cacheDir = $this->getParameter('kernel.cache_dir');
      $this->clearDirectory($cacheDir);

      $this->addFlash('success', 'Cache système vidé avec succès.');
    } catch (\Exception $e) {
      $this->addFlash('error', 'Erreur lors du vidage du cache : ' . $e->getMessage());
    }

    return $this->redirectToRoute('app_admin_parametres');
  }

  #[Route('/parametres/logs', name: 'app_admin_parametres_logs')]
  public function viewLogs(Request $request): Response
  {
    $logFile = $request->query->get('file', 'prod.log');
    $lines = $request->query->getInt('lines', 100);

    $logPath = $this->getParameter('kernel.logs_dir') . '/' . $logFile;
    $logContent = '';

    if (file_exists($logPath)) {
      $logContent = $this->getTailFile($logPath, $lines);
    }

    // Liste des fichiers de logs disponibles
    $logFiles = [];
    $logDir = $this->getParameter('kernel.logs_dir');
    if (is_dir($logDir)) {
      $logFiles = array_filter(scandir($logDir), function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'log';
      });
    }

    return $this->render('admin/parametres/logs.html.twig', [
        'log_content' => $logContent,
        'log_files' => $logFiles,
        'current_file' => $logFile,
        'lines_count' => $lines,
    ]);
  }

  // ============================
  // MÉTHODES AUXILIAIRES POUR LES PARAMÈTRES
  // ============================

  private function handleParametersUpdate(Request $request, EntityManagerInterface $entityManager): Response
  {
    try {
      $parametersData = $request->request->all();

      // Sauvegarder chaque paramètre
      foreach ($parametersData as $key => $value) {
        if (strpos($key, 'param_') === 0) {
          $paramName = substr($key, 6); // Enlever le préfixe 'param_'
          $this->updateParameter($entityManager, $paramName, $value);
        }
      }

      $entityManager->flush();
      $this->addFlash('success', 'Paramètres mis à jour avec succès.');
    } catch (\Exception $e) {
      $this->addFlash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
    }

    return $this->redirectToRoute('app_admin_parametres');
  }

  private function getCurrentParameters(EntityManagerInterface $entityManager): array
  {
    // Vous pouvez créer une entité Parameter ou utiliser une table de configuration
    // Pour cet exemple, je retourne des paramètres par défaut
    return [
        'site_name' => 'CityFlow',
        'site_description' => 'Plateforme de signalement citoyen',
        'contact_email' => 'admin@cityflow.local',
        'max_signalements_per_user' => 10,
        'auto_validation' => false,
        'maintenance_mode' => false,
        'registration_enabled' => true,
        'max_file_size' => 5, // MB
        'notification_email' => true,
        'notification_sms' => false,
        'google_maps_api_key' => '',
        'timezone' => 'Africa/Porto-Novo',
        'language' => 'fr',
        'theme' => 'default',
        'items_per_page' => 20,
    ];
  }

  private function updateParameter(EntityManagerInterface $entityManager, string $name, $value): void
  {
    // Ici vous pouvez implémenter la logique de sauvegarde en base
    // Par exemple avec une entité Parameter
    /*
    $parameter = $parameterRepository->findOneBy(['name' => $name]);
    if (!$parameter) {
        $parameter = new Parameter();
        $parameter->setName($name);
        $entityManager->persist($parameter);
    }
    $parameter->setValue($value);
    */
  }

  private function getSystemInfo(): array
  {
    return [
        'php_version' => PHP_VERSION,
        'symfony_version' => \Symfony\Component\HttpKernel\Kernel::VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'disk_usage' => $this->getDiskUsage(),
        'uptime' => $this->getSystemUptime(),
    ];
  }

  private function getGeneralStats(EntityManagerInterface $entityManager): array
  {
    // Récupérer les statistiques depuis vos repositories existants
    return [
        'database_size' => $this->getDatabaseSize(),
        'total_files' => $this->countUploadedFiles(),
        'cache_size' => $this->getCacheSize(),
        'log_size' => $this->getLogSize(),
    ];
  }

  private function getDiskUsage(): string
  {
    $bytes = disk_total_space('.') - disk_free_space('.');
    return $this->formatBytes($bytes);
  }

  private function getSystemUptime(): string
  {
    if (PHP_OS_FAMILY === 'Windows') {
      return 'Non disponible sur Windows';
    }

    $uptime = shell_exec('uptime -p');
    return $uptime ? trim($uptime) : 'Non disponible';
  }

  private function getDatabaseSize(): string
  {
    // Implémentation pour récupérer la taille de la base de données
    return '0 MB';
  }

  private function countUploadedFiles(): int
  {
    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
    if (!is_dir($uploadDir)) return 0;

    return count(glob($uploadDir . '/*'));
  }

  private function getCacheSize(): string
  {
    $cacheDir = $this->getParameter('kernel.cache_dir');
    return $this->formatBytes($this->getDirectorySize($cacheDir));
  }

  private function getLogSize(): string
  {
    $logDir = $this->getParameter('kernel.logs_dir');
    return $this->formatBytes($this->getDirectorySize($logDir));
  }

  private function getDirectorySize(string $directory): int
  {
    if (!is_dir($directory)) return 0;

    $size = 0;
    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
      $size += $file->getSize();
    }

    return $size;
  }

  private function formatBytes(int $bytes): string
  {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
      $bytes /= 1024;
    }

    return round($bytes, 2) . ' ' . $units[$i];
  }

  private function clearDirectory(string $directory): void
  {
    if (!is_dir($directory)) return;

    $files = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $file) {
      if ($file->isDir()) {
        rmdir($file->getRealPath());
      } else {
        unlink($file->getRealPath());
      }
    }
  }

  private function getTailFile(string $filepath, int $lines): string
  {
    $file = fopen($filepath, 'r');
    $buffer = '';
    $chunk = 4096;

    fseek($file, -$chunk, SEEK_END);

    while (!feof($file)) {
      $buffer = fread($file, $chunk) . $buffer;
    }

    fclose($file);

    $lines_array = explode("\n", $buffer);
    return implode("\n", array_slice($lines_array, -$lines));
  }


  // ========================
  // RAPPORTS MENSUELS - COMPATIBLE AVEC L'EXISTANT
  // ========================

  #[Route('/rapports/mensuel', name: 'app_admin_rapports_mensuel')]
  public function rapportMensuel(
      Request $request,
      SignalementRepository $signalementRepository,
      UtilisateurRepository $utilisateurRepository,
      VilleRepository $villeRepository,
      CategorieRepository $categorieRepository
  ): Response {
    $mois = (int) ($request->query->get('mois') ?? date('n'));
    $annee = (int) ($request->query->get('annee') ?? date('Y'));

    // Validation
    $mois = max(1, min(12, $mois));
    $annee = max(2020, min(date('Y') + 1, $annee));

    // Dates de période
    $dateDebut = new \DateTime("{$annee}-{$mois}-01 00:00:00");
    $dateFin = (clone $dateDebut)->modify('last day of this month')->setTime(23, 59, 59);
    $dateDebutPrecedent = (clone $dateDebut)->modify('-1 month');
    $dateFinPrecedent = (clone $dateDebutPrecedent)->modify('last day of this month')->setTime(23, 59, 59);

    // Générer les données du rapport
    $rapport = $this->generateMonthlyReportOptimized(
        $signalementRepository,
        $utilisateurRepository,
        $villeRepository,
        $categorieRepository,
        $dateDebut,
        $dateFin,
        $dateDebutPrecedent,
        $dateFinPrecedent
    );

    // Nom de la période
    $moisNoms = [
        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
    ];

    $periode = [
        'nom' => $moisNoms[$mois] . ' ' . $annee,
        'mois' => $mois,
        'annee' => $annee
    ];

    return $this->render('admin/rapports/mensuel.html.twig', [
        'rapport' => $rapport,
        'periode' => $periode,
        'mois' => $mois,
        'annee' => $annee,
    ]);
  }


  #[Route('/rapports/export/{format}/{mois}/{annee}', name: 'app_admin_rapports_export')]
  public function exportRapportMensuel(
      string $format,
      int $mois,
      int $annee,
      SignalementRepository $signalementRepository,
      UtilisateurRepository $utilisateurRepository,
      VilleRepository $villeRepository,
      CategorieRepository $categorieRepository
  ): Response
  {
    // Validation
    if (!in_array($format, ['pdf', 'excel', 'csv'])) {
      throw $this->createNotFoundException('Format non supporté');
    }

    $mois = max(1, min(12, $mois));
    $annee = max(2020, min(date('Y') + 1, $annee));

    // Générer les données
    $dateDebut = new \DateTime("{$annee}-{$mois}-01 00:00:00");
    $dateFin = (clone $dateDebut)->modify('last day of this month')->setTime(23, 59, 59);
    $dateDebutPrecedent = (clone $dateDebut)->modify('-1 month');
    $dateFinPrecedent = (clone $dateDebutPrecedent)->modify('last day of this month')->setTime(23, 59, 59);

    $rapport = $this->generateMonthlyReportOptimized(
        $signalementRepository,
        $utilisateurRepository,
        $villeRepository,
        $categorieRepository,
        $dateDebut,
        $dateFin,
        $dateDebutPrecedent,
        $dateFinPrecedent
    );

    // Export selon le format
    switch ($format) {
      case 'pdf':
        return $this->generateRapportPdf($rapport, $mois, $annee);
      case 'excel':
        return $this->generateRapportExcel($rapport, $mois, $annee);
      case 'csv':
        return $this->generateRapportCsv($rapport, $mois, $annee);
      default:
        throw $this->createNotFoundException('Format non supporté');
    }
  }

  // ============================
  // MÉTHODES OPTIMISÉES POUR RAPPORTS MENSUELS
  // ============================

  private function generateMonthlyReportOptimized(
      SignalementRepository $signalementRepo,
      UtilisateurRepository $utilisateurRepo,
      VilleRepository $villeRepo,
      CategorieRepository $categorieRepo,
      \DateTime $dateDebut,
      \DateTime $dateFin,
      \DateTime $dateDebutPrecedent,
      \DateTime $dateFinPrecedent
    ): array {
    // Signalements de la période
    $signalementsPeriode = $signalementRepo->createQueryBuilder('s')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->setParameter('debut', $dateDebut)
        ->setParameter('fin', $dateFin)
        ->getQuery()
        ->getResult();

    $signalementsPrecedent = $signalementRepo->createQueryBuilder('s')
        ->select('COUNT(s.id)')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->setParameter('debut', $dateDebutPrecedent)
        ->setParameter('fin', $dateFinPrecedent)
        ->getQuery()
        ->getSingleScalarResult();

    $totalSignalements = count($signalementsPeriode);

    // Calcul des tendances
    $croissanceSignalements = $signalementsPrecedent > 0
        ? round((($totalSignalements - $signalementsPrecedent) / $signalementsPrecedent) * 100, 1)
        : 0;

    $directionSignalements = $croissanceSignalements > 0 ? 'up' : ($croissanceSignalements < 0 ? 'down' : 'stable');

    // Utilisateurs nouveaux
    $nouveauxUtilisateurs = $utilisateurRepo->createQueryBuilder('u')
        ->select('COUNT(u.id)')
        ->where('u.dateInscription BETWEEN :debut AND :fin')
        ->setParameter('debut', $dateDebut)
        ->setParameter('fin', $dateFin)
        ->getQuery()
        ->getSingleScalarResult();

    // Structure du rapport
    return [
        'signalements' => [
            'total' => $totalSignalements,
            'evolution_quotidienne' => $this->getEvolutionQuotidienne($signalementsPeriode),
            'par_statut' => $this->getRepartitionParStatut($signalementsPeriode),
            'temps_resolution' => $this->calculerTempsResolutionMoyen($signalementsPeriode),
        ],
        'utilisateurs' => [
            'nouveaux' => $nouveauxUtilisateurs,
            'actifs' => $this->compterUtilisateursActifs($utilisateurRepo, $dateDebut, $dateFin),
        ],
        'performance' => [
            'taux_resolution' => $this->calculerTauxResolution($signalementsPeriode),
            'taux_validation' => $this->calculerTauxValidation($signalementsPeriode),
        ],
        'tendances' => [
            'croissance_signalements' => [
                'value' => abs($croissanceSignalements),
                'direction' => $directionSignalements
            ],
            'croissance_utilisateurs' => [
                'value' => 0,
                'direction' => 'stable'
            ]
        ],
        'villes' => [
          // Utiliser la méthode avec repository au lieu du tableau
            'top_signalements' => $this->getTopVilles($signalementRepo, $dateDebut, $dateFin),
            'actives' => $this->compterVillesActives($signalementsPeriode)
        ],
        'categories' => [
          // Pareil pour les catégories
            'top_populaires' => $this->getTopCategories($signalementRepo, $dateDebut, $dateFin)
        ],
        'resume_executif' => [
            'points_cles' => $this->genererPointsCles($totalSignalements, $nouveauxUtilisateurs),
            'niveau_activite' => $this->evaluerNiveauActivite($totalSignalements),
            'recommandations' => $this->genererRecommandations($signalementsPeriode)
        ]
    ];
  }

// Méthodes utilitaires pour le rapport
  private function getEvolutionQuotidienne(array $signalements): array
  {
    $evolution = [];
    $groupes = [];

    foreach ($signalements as $signalement) {
      $jour = $signalement->getDateSignalement()->format('d/m');
      $groupes[$jour] = ($groupes[$jour] ?? 0) + 1;
    }

    foreach ($groupes as $jour => $count) {
      $evolution[] = ['jour' => $jour, 'count' => $count];
    }

    return $evolution;
  }

  private function getRepartitionParStatut(array $signalements): array
  {
    $repartition = [];
    $groupes = [];

    foreach ($signalements as $signalement) {
      $statut = $signalement->getStatut();
      $statutKey = $statut ? $statut->value : 'nouveau'; // Convertir l'enum en string
      $groupes[$statutKey] = ($groupes[$statutKey] ?? 0) + 1;
    }

    $labels = [
        'nouveau' => 'Nouveau',
        'en_cours' => 'En cours',
        'resolu' => 'Résolu',
        'annule' => 'Annulé'
    ];

    foreach ($groupes as $statutKey => $count) {
      $repartition[$statutKey] = [
          'label' => $labels[$statutKey] ?? ucfirst($statutKey),
          'count' => $count
      ];
    }

    return $repartition;
  }

  private function calculerTempsResolutionMoyen(array $signalements): ?float
  {
    $resolus = array_filter($signalements, function($s) {
      return $s->getStatut() === 'resolu' && $s->getDateResolution();
    });

    if (empty($resolus)) return null;

    $totalHeures = 0;
    foreach ($resolus as $signalement) {
      $diff = $signalement->getDateResolution()->diff($signalement->getDateSignalement());
      $totalHeures += ($diff->days * 24) + $diff->h;
    }

    return round($totalHeures / count($resolus), 1);
  }

  private function compterUtilisateursActifs(UtilisateurRepository $repo, \DateTime $debut, \DateTime $fin): int
  {
    return $repo->createQueryBuilder('u')
        ->select('COUNT(DISTINCT u.id)')
        ->leftJoin('u.signalements', 's')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin)
        ->getQuery()
        ->getSingleScalarResult();
  }


  private function calculerTauxResolution(array $signalements): float
  {
    if (empty($signalements)) return 0;

    $resolus = count(array_filter($signalements, function($s) {
      $statut = $s->getStatut();
      return $statut && in_array($statut->value, ['resolu', 'annule']);
    }));

    return round(($resolus / count($signalements)) * 100, 1);
  }

  private function calculerTauxValidation(array $signalements): float
  {
    if (empty($signalements)) return 0;

    $valides = count(array_filter($signalements, function($s) {
      return $s->getEtatValidation() === 'valide';
    }));

    return round(($valides / count($signalements)) * 100, 1);
  }

  private function compterVillesActives(array $signalements): int
  {
    $villes = [];
    foreach ($signalements as $signalement) {
      if ($ville = $signalement->getVille()) {
        $villes[$ville->getId()] = true;
      }
    }

    return count($villes);
  }

  private function genererPointsCles(int $totalSignalements, int $nouveauxUtilisateurs): array
  {
    return [
        "Total de {$totalSignalements} signalements traités ce mois",
        "{$nouveauxUtilisateurs} nouveaux utilisateurs inscrits",
        "Système opérationnel et performant"
    ];
  }

  private function evaluerNiveauActivite(int $totalSignalements): array
  {
    if ($totalSignalements > 100) {
      return ['niveau' => 'Élevé', 'couleur' => 'success', 'icone' => 'arrow-up'];
    } elseif ($totalSignalements > 50) {
      return ['niveau' => 'Modéré', 'couleur' => 'warning', 'icone' => 'minus'];
    } else {
      return ['niveau' => 'Faible', 'couleur' => 'info', 'icone' => 'arrow-down'];
    }
  }

  private function genererRecommandations(array $signalements): array
  {
    $recommendations = [];

    if (count($signalements) > 100) {
      $recommendations[] = "Envisager d'augmenter les ressources de modération";
    }

    $enAttente = count(array_filter($signalements, function($s) {
      return $s->getEtatValidation() === 'en_attente';
    }));

    if ($enAttente > 20) {
      $recommendations[] = "Traiter prioritairement les signalements en attente";
    }

    return $recommendations;
  }


  // ============================
  // NOUVELLES MÉTHODES UTILITAIRES (SANS CONFLIT)
  // ============================

  private function countNewUsersInPeriod(UtilisateurRepository $repo, \DateTime $debut, \DateTime $fin): int
  {
    return $repo->createQueryBuilder('u')
        ->select('COUNT(u.id)')
        ->where('u.dateInscription BETWEEN :debut AND :fin')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin)
        ->getQuery()
        ->getSingleScalarResult();
  }

  private function countActiveUsersInPeriod(UtilisateurRepository $repo, \DateTime $debut, \DateTime $fin): int
  {
    return $repo->createQueryBuilder('u')
        ->select('COUNT(DISTINCT u.id)')
        ->leftJoin('u.signalements', 's')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin)
        ->getQuery()
        ->getSingleScalarResult();
  }

  private function countVillesActives(VilleRepository $repo, \DateTime $debut, \DateTime $fin): int
  {
    return $repo->createQueryBuilder('v')
        ->select('COUNT(DISTINCT v.id)')
        ->leftJoin('v.signalements', 's')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin)
        ->getQuery()
        ->getSingleScalarResult();
  }

  private function countCategoriesUtilisees(CategorieRepository $repo, \DateTime $debut, \DateTime $fin): int
  {
    return $repo->createQueryBuilder('c')
        ->select('COUNT(DISTINCT c.id)')
        ->leftJoin('c.signalements', 's')
        ->where('s.dateSignalement BETWEEN :debut AND :fin')
        ->setParameter('debut', $debut)
        ->setParameter('fin', $fin)
        ->getQuery()
        ->getSingleScalarResult();
  }

  private function getRepartitionRoles(UtilisateurRepository $repo): array
  {
    $users = $repo->findAll();
    $repartition = ['ROLE_USER' => 0, 'ROLE_MODERATEUR' => 0, 'ROLE_ADMIN' => 0];

    foreach ($users as $user) {
      $roles = $user->getRoles();
      if (in_array('ROLE_ADMIN', $roles)) {
        $repartition['ROLE_ADMIN']++;
      } elseif (in_array('ROLE_MODERATEUR', $roles)) {
        $repartition['ROLE_MODERATEUR']++;
      } else {
        $repartition['ROLE_USER']++;
      }
    }

    return $repartition;
  }

  private function calculateGrowthPercentage(?int $current, ?int $previous): array
  {
    if (!$previous || $previous === 0) {
      return [
          'value' => $current > 0 ? 100 : 0,
          'direction' => $current > 0 ? 'up' : 'stable',
          'label' => $current > 0 ? 'Nouveau' : 'Aucun changement',
      ];
    }

    $rate = (($current - $previous) / $previous) * 100;

    return [
        'value' => round(abs($rate), 1),
        'direction' => $rate > 0 ? 'up' : ($rate < 0 ? 'down' : 'stable'),
        'label' => $rate > 0 ? 'Augmentation' : ($rate < 0 ? 'Diminution' : 'Stable'),
    ];
  }

  private function generateResumeExecutifMensuel(array $signalements, array $utilisateurs, array $performance, array $tendances): array
  {
    $points_cles = [];

    // Analyse des signalements
    if ($signalements['total'] > 0) {
      $points_cles[] = "{$signalements['total']} signalements traités ce mois";

      if ($tendances['croissance_signalements']['direction'] === 'up') {
        $points_cles[] = "Augmentation de {$tendances['croissance_signalements']['value']}% par rapport au mois précédent";
      }
    }

    // Analyse de la performance
    if ($performance['taux_resolution'] > 80) {
      $points_cles[] = "Excellent taux de résolution : {$performance['taux_resolution']}%";
    } elseif ($performance['taux_resolution'] > 60) {
      $points_cles[] = "Bon taux de résolution : {$performance['taux_resolution']}%";
    } else {
      $points_cles[] = "Taux de résolution à améliorer : {$performance['taux_resolution']}%";
    }

    // Analyse des utilisateurs
    if ($utilisateurs['nouveaux'] > 0) {
      $points_cles[] = "{$utilisateurs['nouveaux']} nouveaux utilisateurs inscrits";
    }

    return [
        'points_cles' => $points_cles,
        'recommandations' => $this->generateRecommandationsMensuelles($performance, $tendances),
        'niveau_activite' => $this->evaluerNiveauActiviteMensuel($signalements['total']),
    ];
  }

  private function generateRecommandationsMensuelles(array $performance, array $tendances): array
  {
    $recommandations = [];

    if ($performance['taux_resolution'] < 70) {
      $recommandations[] = "Améliorer le processus de résolution des signalements";
    }

    if ($performance['taux_validation'] < 80) {
      $recommandations[] = "Accélérer le processus de validation";
    }

    if ($tendances['croissance_signalements']['direction'] === 'up' && $tendances['croissance_signalements']['value'] > 20) {
      $recommandations[] = "Prévoir des ressources supplémentaires pour gérer l'augmentation";
    }

    return $recommandations;
  }

  private function evaluerNiveauActiviteMensuel(int $totalSignalements): array
  {
    if ($totalSignalements > 100) {
      return ['niveau' => 'Très élevé', 'couleur' => 'success', 'icone' => 'trending-up'];
    } elseif ($totalSignalements > 50) {
      return ['niveau' => 'Élevé', 'couleur' => 'info', 'icone' => 'activity'];
    } elseif ($totalSignalements > 20) {
      return ['niveau' => 'Modéré', 'couleur' => 'warning', 'icone' => 'minus'];
    } else {
      return ['niveau' => 'Faible', 'couleur' => 'secondary', 'icone' => 'trending-down'];
    }
  }

  private function generateSimplePrevision(SignalementRepository $repo, \DateTime $dateDebut): array
  {
    // Calculer une tendance simple basée sur les 3 derniers mois
    $previsions = [];

    for ($i = 1; $i <= 3; $i++) {
      $moisDebut = (clone $dateDebut)->modify("-{$i} month")->modify('first day of this month');
      $moisFin = (clone $moisDebut)->modify('last day of this month');

      $count = $this->getSignalementsByPeriod($repo, $moisDebut, $moisFin, '', '');
      $previsions[] = $count;
    }

    // Calcul simple de tendance (moyenne mobile)
    if (count($previsions) > 0) {
      $moyenne = array_sum($previsions) / count($previsions);
      $tendance = end($previsions) - $previsions[0];

      return [
          'mois_prochain' => round($moyenne + $tendance),
          'tendance' => $tendance > 0 ? 'hausse' : ($tendance < 0 ? 'baisse' : 'stable'),
          'confiance' => 'moyenne',
      ];
    }

    return ['mois_prochain' => 0, 'tendance' => 'stable', 'confiance' => 'faible'];
  }

  // ============================
  // MÉTHODES D'EXPORT SPÉCIFIQUES
  // ============================

  private function generateRapportPdf(array $rapport, int $mois, int $annee): Response
  {
    // TODO: Implémenter la génération PDF avec TCPDF ou DomPDF
    $this->addFlash('info', 'Export PDF en cours de développement.');
    return $this->redirectToRoute('app_admin_rapports_mensuel', ['mois' => $mois, 'annee' => $annee]);
  }

  private function generateRapportExcel(array $rapport, int $mois, int $annee): Response
  {
    // TODO: Implémenter la génération Excel avec PhpSpreadsheet
    $this->addFlash('info', 'Export Excel en cours de développement.');
    return $this->redirectToRoute('app_admin_rapports_mensuel', ['mois' => $mois, 'annee' => $annee]);
  }

  private function generateRapportCsv(array $rapport, int $mois, int $annee): Response
  {
    $filename = "rapport_mensuel_{$annee}_{$mois}.csv";

    $response = new Response();
    $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
    $response->headers->set('Content-Disposition',
        ResponseHeaderBag::DISPOSITION_ATTACHMENT . '; filename="' . $filename . '"'
    );

    $output = fopen('php://temp', 'r+');
    fputs($output, "\xEF\xBB\xBF"); // BOM UTF-8

    // En-têtes et données
    fputcsv($output, ['Rapport Mensuel', $this->getMonthNameFr($mois) . ' ' . $annee], ';');
    fputcsv($output, [], ';'); // Ligne vide

    fputcsv($output, ['=== SIGNALEMENTS ==='], ';');
    fputcsv($output, ['Total signalements', $rapport['signalements']['total']], ';');
    fputcsv($output, ['Taux de résolution', $rapport['performance']['taux_resolution'] . '%'], ';');
    fputcsv($output, ['Taux de validation', $rapport['performance']['taux_validation'] . '%'], ';');

    // Données détaillées
    fputcsv($output, [], ';');
    fputcsv($output, ['=== UTILISATEURS ==='], ';');
    fputcsv($output, ['Nouveaux utilisateurs', $rapport['utilisateurs']['nouveaux']], ';');
    fputcsv($output, ['Utilisateurs actifs', $rapport['utilisateurs']['actifs']], ';');

    rewind($output);
    $content = stream_get_contents($output);
    fclose($output);

    $response->setContent($content);
    return $response;
  }

  // Méthode utilitaire réutilisée
  private function getMonthNameFr(int $month): string
  {
    $months = [
        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
    ];

    return $months[$month] ?? 'Inconnu';
  }



  // Alias pour compatibilité avec le template
  private function getMonthName(int $month): string
  {
    return $this->getMonthNameFr($month);
  }

}
