<?php

namespace App\Controller;

use App\Entity\Signalement;
use App\Entity\JournalValidation;
use App\Enum\EtatValidation;
use App\Repository\SignalementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/validation')]
class ValidationController extends AbstractController
{
    #[Route('/', name: 'app_validation_index')]
    #[IsGranted('ROLE_MODERATOR')]
    public function index(SignalementRepository $signalementRepository): Response
    {
        $user = $this->getUser();

        // Vérifier que l'utilisateur est validé (sauf pour les modérateurs et admins)
        if (!$user->isEstValide() && !$this->isGranted('ROLE_MODERATOR')) {
            $this->addFlash('error', 'Votre compte doit être validé pour accéder à cette page.');
            return $this->redirectToRoute('app_home');
        }

        // Récupérer tous les signalements en attente de validation
        $signalementsEnAttente = $signalementRepository->findBy(
            ['etatValidation' => EtatValidation::EN_ATTENTE->value],
            ['dateSignalement' => 'DESC']
        );

        // Récupérer les signalements récemment traités (pour historique)
        $signalementsTraites = $signalementRepository->createQueryBuilder('s')
            ->where('s.etatValidation != :enAttente')
            ->setParameter('enAttente', EtatValidation::EN_ATTENTE->value)
            ->orderBy('s.dateSignalement', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $this->render('validation/index.html.twig', [
            'signalements_en_attente' => $signalementsEnAttente,
            'signalements_traites' => $signalementsTraites,
        ]);
    }

    #[Route('/valider/{id}', name: 'app_validation_valider')]
    #[IsGranted('ROLE_MODERATOR')]
    public function valider(Signalement $signalement, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Vérifier que l'utilisateur est validé (sauf pour les modérateurs et admins)
        if (!$user->isEstValide() && !$this->isGranted('ROLE_MODERATOR')) {
            $this->addFlash('error', 'Votre compte doit être validé pour effectuer cette action.');
            return $this->redirectToRoute('app_home');
        }

        // Vérifier que le signalements est en attente
        if ($signalement->getEtatValidation() !== EtatValidation::EN_ATTENTE->value) {
            $this->addFlash('warning', 'Ce signalements a déjà été traité.');
            return $this->redirectToRoute('app_signalement_show', ['id' => $signalement->getId()]);
        }

        // Mettre à jour l'état de validation
        $signalement->setEtatValidation(EtatValidation::VALIDE->value);

        // Créer une entrée dans le journal de validation
        $journal = new JournalValidation();
        $journal->setSignalement($signalement);
        $journal->setModerateur($user); // Utiliser setModerateur au lieu de setUtilisateur
        $journal->setDateValidation(new \DateTime()); // Utiliser setDateValidation
        $journal->setAction('Validation');
        $journal->setCommentaire('Le signalements a été validé');

        $entityManager->persist($journal);
        $entityManager->flush();

        $this->addFlash('success', 'Le signalements a été validé avec succès.');

        // Rediriger selon la provenance
        $referer = $request->headers->get('referer');
        if ($referer && str_contains($referer, 'validation')) {
            return $this->redirectToRoute('app_validation_index');
        }

        return $this->redirectToRoute('app_signalement_show', ['id' => $signalement->getId()]);
    }

    #[Route('/rejeter/{id}', name: 'app_validation_rejeter')]
    #[IsGranted('ROLE_MODERATOR')]
    public function rejeter(Signalement $signalement, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Vérifier que l'utilisateur est validé (sauf pour les modérateurs et admins)
        if (!$user->isEstValide() && !$this->isGranted('ROLE_MODERATOR')) {
            $this->addFlash('error', 'Votre compte doit être validé pour effectuer cette action.');
            return $this->redirectToRoute('app_home');
        }

        // Vérifier que le signalements est en attente
        if ($signalement->getEtatValidation() !== EtatValidation::EN_ATTENTE->value) {
            $this->addFlash('warning', 'Ce signalements a déjà été traité.');
            return $this->redirectToRoute('app_signalement_show', ['id' => $signalement->getId()]);
        }

        // Récupérer la raison du rejet depuis la requête
        $raisonRejet = $request->request->get('raison_rejet', 'Aucune raison spécifiée');

        // Mettre à jour l'état de validation
        $signalement->setEtatValidation(EtatValidation::REJETE->value);

        // Créer une entrée dans le journal de validation
        $journal = new JournalValidation();
        $journal->setSignalement($signalement);
        $journal->setModerateur($user); // Utiliser setModerateur au lieu de setUtilisateur
        $journal->setDateValidation(new \DateTime()); // Utiliser setDateValidation
        $journal->setAction('Rejet');
        $journal->setCommentaire('Le signalements a été rejeté. Raison: ' . $raisonRejet);

        $entityManager->persist($journal);
        $entityManager->flush();

        $this->addFlash('success', 'Le signalements a été rejeté.');

        // Rediriger selon la provenance
        $referer = $request->headers->get('referer');
        if ($referer && str_contains($referer, 'validation')) {
            return $this->redirectToRoute('app_validation_index');
        }

        return $this->redirectToRoute('app_signalement_show', ['id' => $signalement->getId()]);
    }
}