<?php

namespace App\Controller;

use App\Entity\Signalement;
use App\Form\ProfilTypeForm;
use App\Repository\MessageRepository;
use App\Repository\SignalementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profil')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ProfilController extends AbstractController
{
    #[Route('/', name: 'app_profil_show')]
    public function index(
        SignalementRepository $signalementRepository,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();

        // Vérifier que l'utilisateur est validé (sauf pour les modérateurs et admins)
        if (!$user->isEstValide() && !$this->isGranted('ROLE_MODERATOR')) {
            $this->addFlash('error', 'Votre compte doit être validé pour accéder à cette page.');
            return $this->redirectToRoute('app_home');
        }

        // Statistiques des signalements
        $signalements = $signalementRepository->findBy(['utilisateur' => $user]);
        $stats = [
            'total' => count($signalements),
            'en_cours' => count(array_filter($signalements, fn($s) => $s->getStatut()->value === 'en_cours')),
            'resolus' => count(array_filter($signalements, fn($s) => $s->getStatut()->value === 'resolu')),
            'rejetes' => count(array_filter($signalements, fn($s) => $s->getStatut()->value === 'rejete')),
        ];

        // Derniers signalements
        $derniersSignalements = $signalementRepository->findBy(
            ['utilisateur' => $user],
            ['dateSignalement' => 'DESC'],
            4
        );

        // Nombre de commentaires de l'utilisateur
        $commentairesCount = $entityManager->createQuery(
            'SELECT COUNT(c.id) FROM App\Entity\Commentaire c WHERE c.utilisateur = :user'
        )->setParameter('user', $user)->getSingleScalarResult();

        // Messages non lus
        $messagesNonLus = $messageRepository->countUnreadMessagesForUser($user);

        // Dernière activité (exemple basique)
        $derniereActivite = [];
        foreach ($derniersSignalements as $signalement) {
            $derniereActivite[] = [
                'icon' => 'fa-exclamation-triangle',
                'description' => 'Signalement créé : ' . $signalement->getTitre(),
                'date' => $signalement->getDateSignalement()
            ];
        }

        // Ajouter aussi les derniers commentaires à l'activité
        $derniersCommentaires = $entityManager->createQuery(
            'SELECT c FROM App\Entity\Commentaire c 
         WHERE c.utilisateur = :user 
         ORDER BY c.dateCommentaire DESC'
    )->setParameter('user', $user)
     ->setMaxResults(3)
     ->getResult();

        foreach ($derniersCommentaires as $commentaire) {
            $derniereActivite[] = [
                'icon' => 'fa-comment',
                'description' => 'Commentaire ajouté sur : ' . $commentaire->getSignalement()->getTitre(),
                'date' => $commentaire->getDateCommentaire()
            ];
        }

        // Trier l'activité par date décroissante
        usort($derniereActivite, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        // Statistiques spéciales pour les modérateurs
        $moderatorStats = [];
        if ($this->isGranted('ROLE_MODERATOR')) {
            // Calculer la date d'aujourd'hui
            $today = new \DateTime();
            $startOfDay = new \DateTime($today->format('Y-m-d') . ' 00:00:00');
            $endOfDay = new \DateTime($today->format('Y-m-d') . ' 23:59:59');
        
            $moderatorStats = [
                'signalements_en_attente' => $signalementRepository->count(['etatValidation' => 'en_attente']),
                'signalements_valides_today' => $entityManager->createQuery(
                    'SELECT COUNT(s.id) FROM App\Entity\Signalement s 
                 WHERE s.etatValidation = :valide 
                 AND s.dateSignalement >= :startOfDay 
                 AND s.dateSignalement <= :endOfDay'
                )->setParameters([
                    'valide' => 'validé',
                    'startOfDay' => $startOfDay,
                    'endOfDay' => $endOfDay
                ])->getSingleScalarResult(),
            ];
        }

        return $this->render('profil/index.html.twig', [
            'signalements_stats' => $stats,
            'derniers_signalements' => $derniersSignalements,
            'commentaires_count' => $commentairesCount,
            'messages_non_lus' => $messagesNonLus,
            'derniere_activite' => array_slice($derniereActivite, 0, 5),
            'moderator_stats' => $moderatorStats,
        ]);
    }

    #[Route('/messages/count', name: 'app_messages_count')]
    public function messagesCount(MessageRepository $messageRepository): JsonResponse
    {
        $user = $this->getUser();
        $count = $messageRepository->countUnreadMessagesForUser($user);

        return new JsonResponse(['count' => $count]);
    }

    #[Route('/edit', name: 'app_profil_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();
        
        // Créer le formulaire avec l'utilisateur connecté
        $form = $this->createForm(ProfilTypeForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement du mot de passe si fourni
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            // Sauvegarder les modifications
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès !');
            return $this->redirectToRoute('app_profil_show');
        }

        return $this->render('profil/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/activites', name: 'app_profil_activites')]
    public function activites(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Récupérer toute l'activité de l'utilisateur
        $signalements = $entityManager->createQuery(
            'SELECT s FROM App\Entity\Signalement s 
             WHERE s.utilisateur = :user 
             ORDER BY s.dateSignalement DESC'
        )->setParameter('user', $user)->getResult();

        $commentaires = $entityManager->createQuery(
            'SELECT c FROM App\Entity\Commentaire c 
             WHERE c.utilisateur = :user 
             ORDER BY c.dateCommentaire DESC'
        )->setParameter('user', $user)->getResult();

        // Combiner toutes les activités
        $activites = [];

        foreach ($signalements as $signalement) {
            $activites[] = [
                'type' => 'signalements',
                'icon' => 'fa-exclamation-triangle',
                'title' => 'Signalement créé',
                'description' => $signalement->getTitre(),
                'date' => $signalement->getDateSignalement(),
                'url' => $this->generateUrl('app_signalement_show', ['id' => $signalement->getId()]),
                'status' => $signalement->getStatut()->value,
            ];
        }

        foreach ($commentaires as $commentaire) {
            $activites[] = [
                'type' => 'commentaire',
                'icon' => 'fa-comment',
                'title' => 'Commentaire ajouté',
                'description' => 'Sur : ' . $commentaire->getSignalement()->getTitre(),
                'date' => $commentaire->getDateCommentaire(),
                'url' => $this->generateUrl('app_signalement_show', ['id' => $commentaire->getSignalement()->getId()]),
                'content' => substr($commentaire->getContenu(), 0, 100) . '...',
            ];
        }

        // Trier par date décroissante
        usort($activites, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return $this->render('profil/activites.html.twig', [
            'activites' => $activites,
            'signalements' => $signalements,
            'commentaires' => $commentaires,
            'user' => $user,
        ]);
    }
}