<?php

namespace App\Controller;

use App\Service\EmailService;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestMailController extends AbstractController
{
    public function __construct(
        private EmailService $emailService
    ) {}

    #[Route('/test-mail', name: 'test_mail')]
    public function test(): Response
    {
        try {
            // Créer un utilisateur test
            $utilisateurTest = new Utilisateur();
            $utilisateurTest->setEmail('koutikaangemarie@gmail.com');
            $utilisateurTest->setNom('Test');
            $utilisateurTest->setPrenom('Web');

            // Envoyer un email de bienvenue via Brevo
            $this->emailService->sendWelcomeEmail($utilisateurTest);

            $this->addFlash('success', 'Email de test envoyé avec succès via Brevo !');
            
            return $this->render('test_mail/index.html.twig', [
                'message' => 'Email envoyé avec succès via Brevo SMTP !',
                'email_sent_to' => 'koutikaangemarie@gmail.com',
                'status' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi : ' . $e->getMessage());
            
            return $this->render('test_mail/index.html.twig', [
                'error' => $e->getMessage(),
                'status' => 'error'
            ]);
        }
    }
}