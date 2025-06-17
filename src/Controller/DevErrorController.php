<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dev')]
class DevErrorController extends AbstractController
{
  #[Route('/error/403', name: 'dev_error_403')]
  public function preview403(): Response
  {
    return $this->render('bundles/TwigBundle/Exception/error403.html.twig', [
        'status_code' => 403,
        'status_text' => 'Forbidden'
    ]);
  }

  #[Route('/error/404', name: 'dev_error_404')]
  public function preview404(): Response
  {
    return $this->render('bundles/TwigBundle/Exception/error404.html.twig', [
        'status_code' => 404,
        'status_text' => 'Not Found'
    ]);
  }

  #[Route('/error/500', name: 'dev_error_500')]
  public function preview500(): Response
  {
    return $this->render('bundles/TwigBundle/Exception/error500.html.twig', [
        'status_code' => 500,
        'status_text' => 'Internal Server Error'
    ]);
  }
}