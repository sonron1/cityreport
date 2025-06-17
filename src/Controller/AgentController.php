<?php

namespace App\Controller;

use App\Repository\SignalementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/agent')]
#[IsGranted('ROLE_AGENT')]
class AgentController extends AbstractController
{
    #[Route('', name: 'app_agent_dashboard')]
    public function index(SignalementRepository $signalementRepository): Response
    {
        $signalements = $signalementRepository->findBy(
            ['estValide' => true],
            ['dateSignalement' => 'DESC']
        );
        
        return $this->render('agent/index.html.twig', [
            'signalements' => $signalements
        ]);
    }
}