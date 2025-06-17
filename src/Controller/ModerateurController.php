<?php

namespace App\Controller;

use App\Entity\JournalValidation;
use App\Entity\Signalement;
use App\Enum\EtatValidation;
use App\Enum\StatutSignalement;
use App\Repository\CategorieRepository;
use App\Repository\JournalValidationRepository;
use App\Repository\SignalementRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/moderateur')]
#[IsGranted('ROLE_MODERATOR')]
class ModerateurController extends AbstractController
{
  // =====================
  // DASHBOARD MODÉRATEUR
  // =====================

  #[Route('', name: 'app_moderateur_dashboard')]
  public function dashboard(
      SignalementRepository $signalementRepository,
      JournalValidationRepository $journalRepository,
      UtilisateurRepository $utilisateurRepository,
      CategorieRepository $categorieRepository
  ): Response {
    $moderateur = $this->getUser();

    // Statistiques corrigées pour utiliser les valeurs d'enum
    $stats = [
        'en_attente' => $signalementRepository->count(['etatValidation' => EtatValidation::EN_ATTENTE->value]),
        'en_cours' => $signalementRepository->count(['statut' => StatutSignalement::EN_COURS->value]),
        'resolus_today' => $this->getSignalementsResolusAujourdhui($signalementRepository),
        'mes_validations' => $this->getMesValidationsAujourdhui($journalRepository, $moderateur),
        'total_signalements' => $signalementRepository->count([]),
        'taux_resolution' => $this->getTauxResolution($signalementRepository)
    ];

    // Signalements prioritaires (en attente)
    $signalements_prioritaires = $signalementRepository->findBy(
        ['etatValidation' => EtatValidation::EN_ATTENTE->value],
        ['dateSignalement' => 'ASC'], // Les plus anciens en premier
        6
    );

    // Mes activités récentes
    $mes_activites = $journalRepository->createQueryBuilder('j')
        ->where('j.moderateur = :moderateur')
        ->setParameter('moderateur', $moderateur)
        ->orderBy('j.dateValidation', 'DESC')
        ->setMaxResults(8)
        ->getQuery()
        ->getResult();

    // Performance du modérateur
    $ma_performance = $this->getMaPerformance($journalRepository, $moderateur);

    // Alertes importantes
    $alertes = $this->getAlertes($signalementRepository);

    // Signalements récents traités
    $signalements_recents = $signalementRepository->createQueryBuilder('s')
        ->where('s.etatValidation IN (:etats)')
        ->setParameter('etats', [EtatValidation::VALIDE->value, EtatValidation::REJETE->value])
        ->orderBy('s.dateSignalement', 'DESC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();

    return $this->render('moderateur/dashboard.html.twig', [
        'stats' => $stats,
        'signalements_prioritaires' => $signalements_prioritaires,
        'mes_activites' => $mes_activites,
        'ma_performance' => $ma_performance,
        'alertes' => $alertes,
        'signalements_recents' => $signalements_recents
    ]);
  }

  // =====================
  // GESTION DES SIGNALEMENTS
  // =====================

  #[Route('/signalements', name: 'app_moderateur_signalements')]
  public function listSignalements(
      SignalementRepository $signalementRepository,
      CategorieRepository $categorieRepository,
      Request $request
  ): Response {
    // Récupérer les paramètres de filtrage
    $filters = [
        'etat' => $request->query->get('etat'),
        'statut' => $request->query->get('statut'),
        'categorie' => $request->query->get('categorie'),
    ];

    // Nettoyer les filtres vides
    $filters = array_filter($filters);

    // ✅ CORRECTION : Afficher TOUS les signalements par défaut
    if (empty($filters)) {
        // Afficher tous les signalements sans filtre
        $signalements = $signalementRepository->findBy(
            [],
            ['dateSignalement' => 'DESC']
        );
    } else {
        // Créer une requête personnalisée avec les filtres appliqués
        $queryBuilder = $signalementRepository->createQueryBuilder('s')
            ->leftJoin('s.ville', 'v')
            ->leftJoin('s.categorie', 'c')
            ->leftJoin('s.utilisateur', 'u');

        // Appliquer les filtres selon leur type
        if (!empty($filters['etat'])) {
            $queryBuilder->andWhere('s.etatValidation = :etat')
                ->setParameter('etat', $filters['etat']);
        }

        if (!empty($filters['statut'])) {
            $queryBuilder->andWhere('s.statut = :statut')
                ->setParameter('statut', $filters['statut']);
        }

        if (!empty($filters['categorie'])) {
            $queryBuilder->andWhere('s.categorie = :categorie')
                ->setParameter('categorie', $filters['categorie']);
        }

        $signalements = $queryBuilder
            ->orderBy('s.dateSignalement', 'DESC')
            ->getQuery()
            ->getResult();
    }

    $categories = $categorieRepository->findAll();

    // Récupérer les statistiques pour le menu latéral
    $stats = $this->getModerateurStats($signalementRepository);

    return $this->render('moderateur/signalements/index.html.twig', [
        'signalements' => $signalements,
        'categories' => $categories,
        'stats' => $stats,
        'currentFilters' => $filters,
    ]);
}

  #[Route('/signalements/{id}', name: 'app_moderateur_signalements_show')]
  public function showSignalement(Signalement $signalement): Response
  {
    return $this->render('moderateur/signalements/show.html.twig', [
        'signalement' => $signalement,
    ]);
  }

  // =====================
  // MODIFICATION DU STATUT
  // =====================

#[Route('/modifier-statut/{id}', name: 'app_moderateur_modifier_statut', methods: ['POST'])]
public function modifierStatut(
    int $id,
    SignalementRepository $signalementRepository,
    EntityManagerInterface $entityManager,
    Request $request
): Response {
    $signalement = $signalementRepository->find($id);

    if (!$signalement) {
        $this->addFlash('error', 'Signalement introuvable.');
        return $this->redirectToRoute('app_moderateur_signalements');
    }

    // Vérifier le token CSRF
    if (!$this->isCsrfTokenValid('modify_status_' . $signalement->getId(), $request->request->get('_token'))) {
        $this->addFlash('error', 'Token de sécurité invalide.');
        return $this->redirectToRoute('app_moderateur_signalements');
    }

    $nouveauStatut = $request->request->get('statut');
    $commentaire = $request->request->get('commentaire', '');


    // ✅ AJOUT : Validation du statut
    if (empty(trim($nouveauStatut))) {
        $this->addFlash('error', 'Veuillez sélectionner un statut.');
        return $this->redirectToRoute('app_moderateur_signalements');
    }

    try {
        // Valider le nouveau statut
        $statutEnum = StatutSignalement::tryFrom($nouveauStatut);
        if (!$statutEnum) {
            // ✅ AJOUT : Message d'erreur plus détaillé
            $this->addFlash('error', "Statut invalide: '$nouveauStatut'. Statuts autorisés: " . implode(', ', array_column(StatutSignalement::cases(), 'value')));
            return $this->redirectToRoute('app_moderateur_signalements');
        }

        // ✅ AJOUT : Vérifier si le statut change vraiment
        if ($signalement->getStatut() === $statutEnum) {
            $this->addFlash('warning', 'Le signalement a déjà ce statut.');
            return $this->redirectToRoute('app_moderateur_signalements');
        }

        // ✅ AJOUT : Vérification spéciale pour le statut "annule"
        if ($statutEnum === StatutSignalement::ANNULE) {
            // Vérifier si l'utilisateur a le droit d'annuler
            if (!$this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('error', 'Seuls les administrateurs peuvent annuler un signalement.');
                return $this->redirectToRoute('app_moderateur_signalements');
            }
            
            // Demander un commentaire obligatoire pour l'annulation
            if (empty(trim($commentaire))) {
                $this->addFlash('error', 'Un commentaire est obligatoire pour annuler un signalement.');
                return $this->redirectToRoute('app_moderateur_signalements');
            }
        }

        $ancienStatut = $signalement->getStatut()->value;
        $signalement->setStatut($statutEnum);

        // Créer une entrée dans le journal
        $journal = new JournalValidation();
        $journal->setSignalement($signalement);
        $journal->setModerateur($this->getUser());
        $journal->setDateValidation(new \DateTime());
        $journal->setAction('Modification du statut');
        
        $journalCommentaire = "Statut modifié de '{$ancienStatut}' vers '{$statutEnum->value}'";
        if (!empty(trim($commentaire))) {
            $journalCommentaire .= ". Commentaire: " . trim($commentaire);
        }
        $journal->setCommentaire($journalCommentaire);

        $entityManager->persist($journal);
        $entityManager->flush();

        $this->addFlash('success', "Statut modifié avec succès vers '{$statutEnum->value}'.");
    } catch (\Exception $e) {
        // ✅ AJOUT : Log de l'erreur pour debug
        $this->addFlash('error', 'Une erreur est survenue lors de la modification du statut: ' . $e->getMessage());
    }

    return $this->redirectToRoute('app_moderateur_signalements');
}

  // =====================
  // VALIDATION DES SIGNALEMENTS
  // =====================
  #[Route('/validation', name: 'app_moderateur_validation')]
  public function validation(SignalementRepository $signalementRepository): Response
  {
    $signalements_en_attente = $signalementRepository->findBy(
        ['etatValidation' => EtatValidation::EN_ATTENTE->value],
        ['dateSignalement' => 'ASC']
    );

    return $this->render('moderateur/validation.html.twig', [
        'signalements_en_attente' => $signalements_en_attente
    ]);
  }

// =====================
// VALIDATION/REJET DES SIGNALEMENTS
// =====================

#[Route('/valider/{id}', name: 'app_moderateur_valider', methods: ['POST'])]
public function validerSignalement(
    int $id,
    SignalementRepository $signalementRepository,
    EntityManagerInterface $entityManager,
    Request $request
): Response {
    $signalement = $signalementRepository->find($id);

    if (!$signalement) {
        $this->addFlash('error', 'Signalement introuvable.');
        return $this->redirectToRoute('app_moderateur_signalements');
    }

    // Vérifier le token CSRF
    if (!$this->isCsrfTokenValid('validate' . $signalement->getId(), $request->request->get('_token'))) {
        $this->addFlash('error', 'Token de sécurité invalide.');
        return $this->redirectToRoute('app_moderateur_signalements');
    }

    try {
        // Changer l'état de validation
        $signalement->setEtatValidation(EtatValidation::VALIDE);

        // Créer une entrée dans le journal
        $journal = new JournalValidation();
        $journal->setSignalement($signalement);
        $journal->setModerateur($this->getUser());
        $journal->setDateValidation(new \DateTime());
        $journal->setAction('Validation');
        $journal->setCommentaire('Signalement validé par le modérateur.');

        $entityManager->persist($journal);
        $entityManager->flush();

        $this->addFlash('success', 'Signalement validé avec succès.');
    } catch (\Exception $e) {
        $this->addFlash('error', 'Une erreur est survenue lors de la validation: ' . $e->getMessage());
    }

    // Rediriger vers la page d'origine ou la liste
    $referer = $request->headers->get('referer');
    if ($referer && str_contains($referer, 'signalements/' . $id)) {
        return $this->redirect($referer);
    }
    
    return $this->redirectToRoute('app_moderateur_signalements');
}

#[Route('/rejeter/{id}', name: 'app_moderateur_rejeter', methods: ['POST'])]
public function rejeterSignalement(
    int $id,
    SignalementRepository $signalementRepository,
    EntityManagerInterface $entityManager,
    Request $request
): Response {
    $signalement = $signalementRepository->find($id);

    if (!$signalement) {
        $this->addFlash('error', 'Signalement introuvable.');
        return $this->redirectToRoute('app_moderateur_signalements');
    }

    // Vérifier le token CSRF
    if (!$this->isCsrfTokenValid('reject' . $signalement->getId(), $request->request->get('_token'))) {
        $this->addFlash('error', 'Token de sécurité invalide.');
        return $this->redirectToRoute('app_moderateur_signalements');
    }

    $commentaire = $request->request->get('commentaire', '');

    // Vérifier qu'un commentaire est fourni
    if (empty(trim($commentaire))) {
        $this->addFlash('error', 'Un motif de rejet est obligatoire.');
        return $this->redirectToRoute('app_moderateur_signalements');
    }

    try {
        // Changer l'état de validation
        $signalement->setEtatValidation(EtatValidation::REJETE);

        // Créer une entrée dans le journal
        $journal = new JournalValidation();
        $journal->setSignalement($signalement);
        $journal->setModerateur($this->getUser());
        $journal->setDateValidation(new \DateTime());
        $journal->setAction('Rejet');
        $journal->setCommentaire('Signalement rejeté. Motif: ' . trim($commentaire));

        $entityManager->persist($journal);
        $entityManager->flush();

        $this->addFlash('success', 'Signalement rejeté avec succès.');
    } catch (\Exception $e) {
        $this->addFlash('error', 'Une erreur est survenue lors du rejet: ' . $e->getMessage());
    }

    // Rediriger vers la page d'origine ou la liste
    $referer = $request->headers->get('referer');
    if ($referer && str_contains($referer, 'signalements/' . $id)) {
        return $this->redirect($referer);
    }
    
    return $this->redirectToRoute('app_moderateur_signalements');
}

  // =====================
  // JOURNAL DE VALIDATION
  // =====================

  #[Route('/journal', name: 'app_moderateur_journal')]
  public function journal(
      JournalValidationRepository $journalRepository,
      SignalementRepository $signalementRepository,
      Request $request
  ): Response {
    $moderateur = $this->getUser();
    $page = $request->query->getInt('page', 1);
    $limit = 20;

    // Récupérer le journal pour ce modérateur avec pagination simple
    $offset = ($page - 1) * $limit;

    $journal = $journalRepository->createQueryBuilder('j')
        ->where('j.moderateur = :moderateur')
        ->setParameter('moderateur', $moderateur)
        ->orderBy('j.dateValidation', 'DESC')
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();

    // Compter le total pour la pagination
    $totalItems = $journalRepository->createQueryBuilder('j')
        ->select('COUNT(j.id)')
        ->where('j.moderateur = :moderateur')
        ->setParameter('moderateur', $moderateur)
        ->getQuery()
        ->getSingleScalarResult();

    $totalPages = ceil($totalItems / $limit);

    // ✅ AJOUT : Récupérer les stats nécessaires pour le template
    $stats = $this->getModerateurStats($signalementRepository);

    return $this->render('moderateur/journal.html.twig', [
        'journal' => $journal,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalItems' => $totalItems,
        'stats' => $stats, // ✅ AJOUT de la variable stats
    ]);
  }

  // =====================
  // STATISTIQUES MODÉRATEUR
  // =====================

  #[Route('/statistiques', name: 'app_moderateur_statistiques')]
  public function statistiques(
      SignalementRepository $signalementRepository,
      JournalValidationRepository $journalRepository
  ): Response {
    $moderateur = $this->getUser();

    // Statistiques détaillées pour le modérateur
    $stats = [
        'mes_validations_totales' => $this->getMesValidationsTotales($journalRepository, $moderateur),
        'performance_mensuelle' => $this->getPerformanceMensuelle($journalRepository, $moderateur),
        'repartition_actions' => $this->getRepartitionActions($journalRepository, $moderateur),
        'evolution_performance' => $this->getEvolutionPerformance($journalRepository, $moderateur),
        'comparaison_equipe' => $this->getComparaisonEquipe($journalRepository, $moderateur),
    ];

    return $this->render('moderateur/statistiques.html.twig', [
        'stats' => $stats
    ]);
  }

  // =====================
  // MÉTHODES PRIVÉES
  // =====================

  private function getSignalementsResolusAujourdhui(SignalementRepository $signalementRepository): int
  {
    $today = new \DateTime('today');
    $tomorrow = new \DateTime('tomorrow');

    return $signalementRepository->createQueryBuilder('s')
        ->select('COUNT(s.id)')
        ->where('s.statut = :statut')
        ->andWhere('s.dateSignalement >= :today')
        ->andWhere('s.dateSignalement < :tomorrow')
        ->setParameter('statut', StatutSignalement::RESOLU->value)
        ->setParameter('today', $today)
        ->setParameter('tomorrow', $tomorrow)
        ->getQuery()
        ->getSingleScalarResult();
  }

  private function getMesValidationsAujourdhui(JournalValidationRepository $journalRepository, $moderateur): int
  {
    $today = new \DateTime('today');
    $tomorrow = new \DateTime('tomorrow');

    return $journalRepository->createQueryBuilder('j')
        ->select('COUNT(j.id)')
        ->where('j.moderateur = :moderateur')
        ->andWhere('j.dateValidation >= :today')
        ->andWhere('j.dateValidation < :tomorrow')
        ->setParameter('moderateur', $moderateur)
        ->setParameter('today', $today)
        ->setParameter('tomorrow', $tomorrow)
        ->getQuery()
        ->getSingleScalarResult();
  }

  private function getTauxResolution(SignalementRepository $signalementRepository): float
  {
    $total = $signalementRepository->count([]);
    if ($total === 0) return 0;

    $resolus = $signalementRepository->count(['statut' => StatutSignalement::RESOLU->value]);
    return round(($resolus / $total) * 100, 1);
  }

  private function getMaPerformance(JournalValidationRepository $journalRepository, $moderateur): array
  {
    $startOfWeek = new \DateTime('monday this week');
    $endOfWeek = new \DateTime('sunday this week 23:59:59');

    $validationsThisWeek = $journalRepository->createQueryBuilder('j')
        ->select('COUNT(j.id)')
        ->where('j.moderateur = :moderateur')
        ->andWhere('j.dateValidation BETWEEN :start AND :end')
        ->setParameter('moderateur', $moderateur)
        ->setParameter('start', $startOfWeek)
        ->setParameter('end', $endOfWeek)
        ->getQuery()
        ->getSingleScalarResult();

    $validationsToday = $this->getMesValidationsAujourdhui($journalRepository, $moderateur);

    return [
        'validations_this_week' => $validationsThisWeek,
        'validations_today' => $validationsToday,
        'moyenne_quotidienne' => round($validationsThisWeek / 7, 1),
    ];
  }

  private function getAlertes(SignalementRepository $signalementRepository): array
  {
    $alertes = [];

    // Vérifier les signalements anciens en attente
    $signalementsAnciens = $signalementRepository->createQueryBuilder('s')
        ->select('COUNT(s.id)')
        ->where('s.etatValidation = :etat')
        ->andWhere('s.dateSignalement < :date')
        ->setParameter('etat', EtatValidation::EN_ATTENTE->value)
        ->setParameter('date', new \DateTime('-7 days'))
        ->getQuery()
        ->getSingleScalarResult();

    if ($signalementsAnciens > 0) {
      $alertes[] = [
          'type' => 'warning',
          'message' => "$signalementsAnciens signalement(s) en attente depuis plus de 7 jours"
      ];
    }

    // Vérifier le pic de signalements
    $signalementsAujourdhui = $signalementRepository->createQueryBuilder('s')
        ->select('COUNT(s.id)')
        ->where('s.dateSignalement >= :today')
        ->setParameter('today', new \DateTime('today'))
        ->getQuery()
        ->getSingleScalarResult();

    if ($signalementsAujourdhui > 20) {
      $alertes[] = [
          'type' => 'info',
          'message' => "Pic d'activité: $signalementsAujourdhui nouveaux signalements aujourd'hui"
      ];
    }

    return $alertes;
  }

  private function getMesValidationsTotales(JournalValidationRepository $journalRepository, $moderateur): int
  {
    return $journalRepository->createQueryBuilder('j')
        ->select('COUNT(j.id)')
        ->where('j.moderateur = :moderateur')
        ->setParameter('moderateur', $moderateur)
        ->getQuery()
        ->getSingleScalarResult();
  }

  private function getPerformanceMensuelle(JournalValidationRepository $journalRepository, $moderateur): array
  {
    $data = [];

    for ($i = 11; $i >= 0; $i--) {
      $date = new \DateTime("-$i months");
      $startMonth = clone $date;
      $startMonth->modify('first day of this month');
      $endMonth = clone $date;
      $endMonth->modify('last day of this month 23:59:59');

      $count = $journalRepository->createQueryBuilder('j')
          ->select('COUNT(j.id)')
          ->where('j.moderateur = :moderateur')
          ->andWhere('j.dateValidation BETWEEN :start AND :end')
          ->setParameter('moderateur', $moderateur)
          ->setParameter('start', $startMonth)
          ->setParameter('end', $endMonth)
          ->getQuery()
          ->getSingleScalarResult();

      $data[] = [
          'mois' => $date->format('M Y'),
          'validations' => (int) $count
      ];
    }

    return $data;
  }

  private function getRepartitionActions(JournalValidationRepository $journalRepository, $moderateur): array
  {
    $results = $journalRepository->createQueryBuilder('j')
        ->select('j.action, COUNT(j.id) as count')
        ->where('j.moderateur = :moderateur')
        ->setParameter('moderateur', $moderateur)
        ->groupBy('j.action')
        ->getQuery()
        ->getResult();

    $data = [];
    foreach ($results as $result) {
      $data[$result['action']] = (int) $result['count'];
    }

    return $data;
  }

  private function getEvolutionPerformance(JournalValidationRepository $journalRepository, $moderateur): array
  {
    $data = [];

    for ($i = 29; $i >= 0; $i--) {
      $date = new \DateTime("-$i days");
      $startDay = clone $date;
      $startDay->setTime(0, 0, 0);
      $endDay = clone $date;
      $endDay->setTime(23, 59, 59);

      $count = $journalRepository->createQueryBuilder('j')
          ->select('COUNT(j.id)')
          ->where('j.moderateur = :moderateur')
          ->andWhere('j.dateValidation BETWEEN :start AND :end')
          ->setParameter('moderateur', $moderateur)
          ->setParameter('start', $startDay)
          ->setParameter('end', $endDay)
          ->getQuery()
          ->getSingleScalarResult();

      $data[] = [
          'date' => $date->format('d/m'),
          'validations' => (int) $count
      ];
    }

    return $data;
  }

  private function getComparaisonEquipe(JournalValidationRepository $journalRepository, $moderateur): array
  {
    $startMonth = new \DateTime('first day of this month');
    $endMonth = new \DateTime('last day of this month 23:59:59');

    // Performance du modérateur actuel
    $mesValidations = $journalRepository->createQueryBuilder('j')
        ->select('COUNT(j.id)')
        ->where('j.moderateur = :moderateur')
        ->andWhere('j.dateValidation BETWEEN :start AND :end')
        ->setParameter('moderateur', $moderateur)
        ->setParameter('start', $startMonth)
        ->setParameter('end', $endMonth)
        ->getQuery()
        ->getSingleScalarResult();

    // Moyenne de l'équipe (simplifiée)
    $moyenneEquipe = $journalRepository->createQueryBuilder('j')
        ->select('COUNT(j.id)')
        ->where('j.dateValidation BETWEEN :start AND :end')
        ->setParameter('start', $startMonth)
        ->setParameter('end', $endMonth)
        ->getQuery()
        ->getSingleScalarResult();

    // Diviser par le nombre de modérateurs (estimation)
    $moyenneEquipe = $moyenneEquipe > 0 ? round($moyenneEquipe / 3, 1) : 0;

    return [
        'mes_validations' => (int) $mesValidations,
        'moyenne_equipe' => $moyenneEquipe,
        'position' => $mesValidations > $moyenneEquipe ? 'above' : 'below'
    ];
  }

  /**
   * Méthode privée pour obtenir les statistiques utilisées dans la sidebar
   */
  private function getModerateurStats(SignalementRepository $signalementRepository): array
  {
    return [
        'signalementsEnAttente' => $signalementRepository->count(['etatValidation' => EtatValidation::EN_ATTENTE]),
        'signalementsEnCours' => $signalementRepository->count(['statut' => StatutSignalement::EN_COURS]),
        'signalementsResolus' => $signalementRepository->count(['statut' => StatutSignalement::RESOLU]),
        'signalementsRejetes' => $signalementRepository->count(['etatValidation' => EtatValidation::REJETE]),
        // ✅ AJOUT : Clés manquantes pour le template admin
        'totalSignalements' => $signalementRepository->count([]),
        'totalUtilisateurs' => 0, // Les modérateurs n'ont pas accès à ces stats
        'demandesSuppressions' => 0, // Les modérateurs n'ont pas accès à ces stats
        'utilisateursNonValides' => 0, // Les modérateurs n'ont pas accès à ces stats
        'totalCategories' => 0, // Les modérateurs n'ont pas accès à ces stats
        'totalVilles' => 0, // Les modérateurs n'ont pas accès à ces stats
    ];
  }
}