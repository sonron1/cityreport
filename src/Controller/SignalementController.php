<?php

namespace App\Controller;

use App\Entity\Signalement;
use App\Entity\Utilisateur;
use App\Enum\StatutSignalement;
use App\Form\SignalementTypeForm;
use App\EventListener\AccessDeniedListener;
use App\Repository\SignalementRepository;
use App\Repository\CategorieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Enum\DemandeSuppressionStatut;
use App\Entity\JournalValidation;
use App\Entity\Commentaire;
use App\Form\CommentaireTypeForm;
use App\Enum\EtatValidation;
use App\Service\NotificationService;
use Psr\Log\LoggerInterface; // ✅ AJOUT DU LOGGER SYMFONY

class SignalementController extends AbstractController
{
    // ✅ INJECTION DES SERVICES
    public function __construct(
        private NotificationService $notificationService,
        private LoggerInterface $logger
    ) {}

    #[Route('/signalement', name: 'app_signalements')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        Request $request,
        SignalementRepository $signalementRepository,
        VilleRepository $villeRepository,
        CategorieRepository $categorieRepository
    ): Response {
        $user = $this->getUser();

        // Vérifier que l'utilisateur est validé (sauf pour les modérateurs et admins)
        if (!$user->isEstValide() && !$this->isGranted('ROLE_MODERATOR')) {
            $this->addFlash('error', 'Votre compte doit être validé pour accéder aux signalements.');
            return $this->redirectToRoute('app_home');
        }

        $page = max(1, $request->query->getInt('page', 1));
        $itemsPerPage = 9;
        
        // ✅ CORRECTION : Récupérer l'onglet actuel et le conserver
        $currentTab = $request->query->get('tab', 'my');

        // Préparation des filtres
        $filters = [
            'statut' => $request->query->get('statut'),
            'categorie' => $request->query->has('categorie') ? $request->query->get('categorie') : null,
            'ville' => $request->query->has('ville') ? $request->query->get('ville') : null,
            'date' => $request->query->get('date'),
        ];

        // Filtrer les valeurs vides
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });

        // Récupérer les villes et catégories pour les filtres
        $villes = $villeRepository->findAll();
        $categories = $categorieRepository->findAll();

        // Initialiser les variables
        $userSignalements = null;
        $allSignalements = null;
        $userSignalementsEnCours = 0;
        $userSignalementsResolus = 0;
        $userSignalementsRejetes = 0;

        try {
            if ($this->isGranted('ROLE_MODERATOR')) {
                // Les modérateurs voient tous les signalements par défaut
                if ($currentTab === 'all') {
                    $allSignalements = $signalementRepository->findAllSignalementsPaginated(
                        $page,
                        $itemsPerPage,
                        $filters,
                        true // Inclure les non-validés pour les modérateurs
                    );
                    $userSignalements = $signalementRepository->findUserSignalementsPaginated(
                        $user,
                        1,
                        $itemsPerPage,
                        $filters
                    );
                } else {
                    $userSignalements = $signalementRepository->findUserSignalementsPaginated(
                        $user,
                        $page,
                        $itemsPerPage,
                        $filters
                    );
                    $allSignalements = $signalementRepository->findAllSignalementsPaginated(
                        1,
                        $itemsPerPage,
                        $filters,
                        true
                    );
                }

                // Statistiques pour les modérateurs
                $userSignalementsEnCours = $signalementRepository->countUserSignalementsByStatus($user, 'en_cours');
                $userSignalementsResolus = $signalementRepository->countUserSignalementsByStatus($user, 'resolu');
                $userSignalementsRejetes = $signalementRepository->countUserSignalementsByStatus($user, 'rejete');
            } else {
                // Utilisateurs normaux
                if ($currentTab === 'all') {
                    $allSignalements = $signalementRepository->findAllPublicSignalementsPaginated(
                        $page,
                        $itemsPerPage,
                        $filters
                    );
                    $userSignalements = $signalementRepository->findUserSignalementsPaginated(
                        $user,
                        1,
                        $itemsPerPage,
                        $filters
                    );
                } else {
                    $userSignalements = $signalementRepository->findUserSignalementsPaginated(
                        $user,
                        $page,
                        $itemsPerPage,
                        $filters
                    );
                    $allSignalements = $signalementRepository->findAllPublicSignalementsPaginated(
                        1,
                        $itemsPerPage,
                        $filters
                    );
                }

                $userSignalementsEnCours = $signalementRepository->countUserSignalementsByStatus($user, 'en_cours');
                $userSignalementsResolus = $signalementRepository->countUserSignalementsByStatus($user, 'resolu');
                $userSignalementsRejetes = $signalementRepository->countUserSignalementsByStatus($user, 'rejete');
            }

        } catch (\Exception $e) {
            // ✅ LOGGING PROFESSIONNEL
            $this->logger->error('Erreur lors du chargement des signalements', [
                'user_id' => $user->getId(),
                'filters' => $filters,
                'exception' => $e->getMessage()
            ]);
            
            $this->addFlash('error', 'Erreur lors du chargement des signalements. Veuillez réessayer.');

            // Valeurs par défaut pour éviter les erreurs
            $userSignalements = new \App\Pagination\Paginator(new \Doctrine\ORM\Tools\Pagination\Paginator($this->createQueryBuilder('s')->where('1=0')->getQuery()), 1, $itemsPerPage);
            $allSignalements = new \App\Pagination\Paginator(new \Doctrine\ORM\Tools\Pagination\Paginator($this->createQueryBuilder('s')->where('1=0')->getQuery()), 1, $itemsPerPage);
        }

        return $this->render('signalement/index.html.twig', [
            'user_signalements_paginator' => $userSignalements,
            'user_signalements_en_cours' => $userSignalementsEnCours,
            'user_signalements_resolus' => $userSignalementsResolus,
            'user_signalements_rejetes' => $userSignalementsRejetes,
            'all_signalements_paginator' => $allSignalements,
            'villes' => $villes,
            'categories' => $categories,
            'is_moderator' => $this->isGranted('ROLE_MODERATOR'),
            'current_tab' => $currentTab, // ✅ IMPORTANT : Passer l'onglet actuel au template
            'current_filters' => $filters, // ✅ Passer les filtres actuels
        ]);
    }

    #[Route('/signalements/{id}', name: 'app_signalement_show', requirements: ['id' => '\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(Signalement $signalement): Response
    {
        $user = $this->getUser();

        // Vérifier que l'utilisateur est validé (sauf pour les modérateurs et admins)
        if (!$user->isEstValide() && !$this->isGranted('ROLE_MODERATOR')) {
            $this->addFlash('error', 'Votre compte doit être validé pour accéder aux signalements.');
            return $this->redirectToRoute('app_home');
        }

        // Créer un nouvel objet Commentaire
        $commentaire = new Commentaire();
        $commentaire->setSignalement($signalement);

        // Créer le formulaire
        $commentForm = $this->createForm(CommentaireTypeForm::class, $commentaire, [
            'action' => $this->generateUrl('app_signalement_add_commentaire', ['id' => $signalement->getId()])
        ]);

        // Rendre la vue avec le formulaire
        return $this->render('signalement/show.html.twig', [
            'signalement' => $signalement,
            'commentForm' => $commentForm->createView()
        ]);
    }

    #[Route('/signalements/nouveau', name: 'app_signalement_nouveau')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function nouveau(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();

        // Vérifier que l'utilisateur est validé
        if (!$user->isEstValide()) {
            $this->addFlash('error', 'Votre compte doit être validé pour créer des signalements.');
            return $this->redirectToRoute('app_home');
        }

        $signalement = new Signalement();
        $signalement->setUtilisateur($user);
        $signalement->setDateSignalement(new \DateTime());
        $signalement->setStatut(StatutSignalement::NOUVEAU);
        $signalement->setEtatValidation(EtatValidation::EN_ATTENTE);

        $form = $this->createForm(SignalementTypeForm::class, $signalement, [
            'arrondissement_url' => $this->generateUrl('app_arrondissement_api_by_ville', ['id' => '_villeId_'])
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ CONTRÔLE OBLIGATOIRE DE LA PHOTO
            $photoFile = $form->get('photo')->getData();

            if (!$photoFile) {
                $this->addFlash('error', 'Une photo est obligatoire pour créer un signalement.');
                return $this->render('signalement/nouveau.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Gestion de l'upload de photo
            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid('', true).'.'.$photoFile->guessExtension();

            try {
                $photoFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );
                $signalement->setPhotoUrl($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image.');
                return $this->render('signalement/nouveau.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // ✅ VÉRIFICATION FINALE AVANT PERSISTANCE
            if (!$signalement->getPhotoUrl()) {
                $this->addFlash('error', 'Une photo est obligatoire pour créer un signalement.');
                return $this->render('signalement/nouveau.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $entityManager->persist($signalement);
            $entityManager->flush();

            $this->addFlash('success', 'Votre signalement a été créé avec succès.');
            return $this->redirectToRoute('app_signalements');
        }

        return $this->render('signalement/nouveau.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mes-signalements', name: 'app_mes_signalements')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function mesSignalements(SignalementRepository $signalementRepository): Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        // Vérifier que l'utilisateur est validé
        if (!$utilisateur->isEstValide()) {
            $this->addFlash('error', 'Votre compte doit être validé pour accéder à vos signalements.');
            return $this->redirectToRoute('app_home');
        }

        $signalements = $signalementRepository->findBy(
            ['utilisateur' => $utilisateur],
            ['dateSignalement' => 'DESC']
        );

        return $this->render('signalement/mes_signalements.html.twig', [
            'signalements' => $signalements,
        ]);
    }

    #[Route('/signalements/{id}/delete', name: 'app_signalement_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Signalement $signalement,
        EntityManagerInterface $entityManager
    ): Response
    {
        try {
            // Créer une entrée dans le journal (si besoin de garder une trace)
            $journal = new JournalValidation();
            $journal->setSignalement($signalement);
            $journal->setModerateur($this->getUser());
            $journal->setDateValidation(new \DateTime());
            $journal->setAction('Suppression définitive');
            $journal->setCommentaire('Signalement "' . $signalement->getTitre() . '" supprimé par ' . $this->getUser()->getPrenom() . ' ' . $this->getUser()->getNom());

            // Persister le journal avant la suppression
            $entityManager->persist($journal);
            $entityManager->flush();

            // Supprimer le signalement
            $entityManager->remove($signalement);
            $entityManager->flush();

            $this->addFlash('success', 'Le signalement a été supprimé avec succès.');
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la suppression du signalement', [
                'signalement_id' => $signalement->getId(),
                'user_id' => $this->getUser()->getId(),
                'exception' => $e->getMessage()
            ]);
            
            $this->addFlash('error', 'Erreur lors de la suppression du signalement : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_signalements');
    }

    #[Route('/signalements/{id}/modifier', name: 'app_signalement_modifier')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function modifier(
        Signalement $signalement,
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $user = $this->getUser();

        // Vérifier que l'utilisateur est propriétaire du signalement
        if ($signalement->getUtilisateur() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres signalements.');
        }

        // Vérifier que le signalement est rejeté
        if ($signalement->getEtatValidation() !== EtatValidation::REJETE) {
            $this->addFlash('error', 'Seuls les signalements rejetés peuvent être modifiés.');
            return $this->redirectToRoute('app_mes_signalements');
        }

        $form = $this->createForm(SignalementTypeForm::class, $signalement, [
            'arrondissement_url' => $this->generateUrl('app_arrondissement_api_by_ville', ['id' => '_villeId_'])
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de nouvelle photo (optionnel)
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                // Supprimer l'ancienne photo si elle existe
                if ($signalement->getPhotoUrl()) {
                    $anciennePhoto = $this->getParameter('photos_directory') . '/' . $signalement->getPhotoUrl();
                    if (file_exists($anciennePhoto)) {
                        unlink($anciennePhoto);
                    }
                }

                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid('', true).'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                    $signalement->setPhotoUrl($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_signalement_modifier', ['id' => $signalement->getId()]);
                }
            }

            // Remettre en attente de validation
            $signalement->setEtatValidation(EtatValidation::EN_ATTENTE);

            // Créer une entrée dans le journal
            $journal = new JournalValidation();
            $journal->setSignalement($signalement);
            $journal->setModerateur($user);
            $journal->setDateValidation(new \DateTime());
            $journal->setAction('Modification après rejet');
            $journal->setCommentaire('L\'utilisateur a modifié son signalement suite au rejet');

            $entityManager->persist($journal);
            $entityManager->flush();

            $this->addFlash('success', 'Votre signalement a été modifié et soumis à nouveau pour validation !');
            return $this->redirectToRoute('app_mes_signalements');
        }

        return $this->render('signalement/modifier.html.twig', [
            'form' => $form->createView(),
            'signalement' => $signalement,
        ]);
    }

    #[Route('/signalements/{id}/demande-suppression', name: 'app_signalement_demande_suppression', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function demandeSuppressionAction(
        int $id,
        Request $request,
        SignalementRepository $signalementRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $signalement = $signalementRepository->find($id);

        if (!$signalement) {
            throw $this->createNotFoundException('Signalement introuvable.');
        }

        // Vérifier que l'utilisateur est propriétaire du signalement
        if ($signalement->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce signalement.');
        }

        // Vérification du token CSRF avec le bon nom
        if (!$this->isCsrfTokenValid('delete_request' . $signalement->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token de sécurité invalide.');
            return $this->redirectToRoute('app_mes_signalements');
        }

        // Vérifier qu'une demande n'est pas déjà en cours
        if ($signalement->getDemandeSuppressionStatut() === 'demandee') {
            $this->addFlash('warning', 'Une demande de suppression est déjà en cours pour ce signalement.');
            return $this->redirectToRoute('app_mes_signalements');
        }

        try {
            $signalement->setDemandeSuppressionStatut('demandee');
            $signalement->setDateDemandeSuppressionStatut(new \DateTime());

            $entityManager->flush();

            $this->addFlash('success', 'Votre demande de suppression a été soumise. Elle sera examinée par un administrateur.');
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la demande de suppression', [
                'signalement_id' => $signalement->getId(),
                'user_id' => $this->getUser()->getId(),
                'exception' => $e->getMessage()
            ]);
            
            $this->addFlash('error', 'Une erreur est survenue lors de la soumission de votre demande.');
        }

        return $this->redirectToRoute('app_mes_signalements');
    }

    // ✅ MÉTHODE CORRIGÉE AVEC LOGGING PROFESSIONNEL
    #[Route('/signalements/{id}/commentaire', name: 'app_signalement_add_commentaire', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function addCommentaire(
        int $id,
        Request $request,
        SignalementRepository $signalementRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $signalement = $signalementRepository->find($id);
        if (!$signalement) {
            throw $this->createNotFoundException('Signalement non trouvé');
        }

        $user = $this->getUser();

        // ✅ VÉRIFICATION : L'utilisateur a-t-il déjà commenté ?
        $commentaireExistant = $entityManager->getRepository(Commentaire::class)
            ->findOneBy([
                'signalement' => $signalement,
                'utilisateur' => $user
            ]);

        if ($commentaireExistant) {
            $this->addFlash('error', '⚠️ Vous avez déjà posté un commentaire sur ce signalement. Vous pouvez le modifier si nécessaire.');
            return $this->redirectToRoute('app_signalement_show', ['id' => $id]);
        }

        $contenu = $request->request->get('contenu');
        if (!$contenu || trim($contenu) === '') {
            $this->addFlash('error', 'Le commentaire ne peut pas être vide.');
            return $this->redirectToRoute('app_signalement_show', ['id' => $id]);
        }

        // Créer le nouveau commentaire
        $commentaire = new Commentaire();
        $commentaire->setContenu(trim($contenu));
        $commentaire->setSignalement($signalement);
        $commentaire->setUtilisateur($user);
        $commentaire->setEtatValidation('valide'); // Auto-validation des commentaires

        $entityManager->persist($commentaire);
        $entityManager->flush();

        // ✅ NOTIFICATION AVEC LOGGING PROFESSIONNEL
        try {
            $this->notificationService->notifierNouveauCommentaire($signalement, $user);
        } catch (\Exception $e) {
            $this->logger->warning('Échec de l\'envoi de notification pour nouveau commentaire', [
                'signalement_id' => $signalement->getId(),
                'user_id' => $user->getId(),
                'commentaire_id' => $commentaire->getId(),
                'exception' => $e->getMessage()
            ]);
        }

        $this->addFlash('success', '✅ Votre commentaire a été ajouté avec succès !');
        return $this->redirectToRoute('app_signalement_show', ['id' => $id]);
    }

    /**
     * ✅ MÉTHODE POUR MODIFIER UN COMMENTAIRE
     */
    #[Route('/commentaires/{id}/modifier', name: 'app_commentaire_modifier', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function modifierCommentaire(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $commentaire = $entityManager->getRepository(Commentaire::class)->find($id);

        if (!$commentaire) {
            throw $this->createNotFoundException('Commentaire non trouvé');
        }

        // ✅ SÉCURITÉ : Seul l'auteur peut modifier son commentaire
        if ($commentaire->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres commentaires.');
        }

        $nouveauContenu = $request->request->get('contenu');
        if (!$nouveauContenu || trim($nouveauContenu) === '') {
            $this->addFlash('error', 'Le commentaire ne peut pas être vide.');
            return $this->redirectToRoute('app_signalement_show', ['id' => $commentaire->getSignalement()->getId()]);
        }

        $commentaire->setContenu(trim($nouveauContenu));
        $commentaire->setDateCommentaire(new \DateTime()); // Mettre à jour la date

        $entityManager->flush();

        $this->addFlash('success', '✅ Votre commentaire a été modifié avec succès !');
        return $this->redirectToRoute('app_signalement_show', ['id' => $commentaire->getSignalement()->getId()]);
    }
}