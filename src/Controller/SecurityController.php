<?php

namespace App\Controller;

// En haut du fichier, ajoutez ces importations si elles ne sont pas déjà présentes
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    
    #[Route(path: '/compte-non-verifie', name: 'app_not_verified')]
    public function notVerified(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        if ($this->getUser()->isEstValide()) {
            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('security/not_verified.html.twig');
    }
// Ajouter cette méthode à votre SecurityController

/**
 * @Route("/check-verification", name="app_check_verification")
 */
#[Route('/check-verification', name: 'app_check_verification')]
public function checkVerification(Request $request): Response
{
    $session = $request->getSession();
    
    if ($session->has('_security_verification_needed')) {
        $session->remove('_security_verification_needed');
        return $this->redirectToRoute('app_not_verified');
    }
    
    // Si l'utilisateur est connecté mais pas vérifié, rediriger également
    $user = $this->getUser();
    if ($user instanceof Utilisateur && !$user->isEstValide()) {
        return $this->redirectToRoute('app_not_verified');
    }
    
    // Sinon, rediriger vers la page d'accueil
    return $this->redirectToRoute('app_home');
}
}