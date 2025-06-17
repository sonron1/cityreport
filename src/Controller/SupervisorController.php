<?php

namespace App\Controller;

use App\Repository\SignalementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/supervisor')]
#[IsGranted('ROLE_SUPERVISOR')]
class SupervisorController extends AbstractController
{
    #[Route('', name: 'app_supervisor_dashboard')]
    public function index(SignalementRepository $signalementRepository): Response
    {
        return $this->render('supervisor/index.html.twig');
    }
}