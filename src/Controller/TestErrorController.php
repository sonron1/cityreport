<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TestErrorController extends AbstractController
{
  #[Route('/test/error/404', name: 'test_error_404')]
  public function test404(): Response
  {
    throw new NotFoundHttpException('Page de test 404');
  }

  #[Route('/test/error/403', name: 'test_error_403')]
  public function test403(): Response
  {
    throw new AccessDeniedHttpException('Page de test 403');
  }

  #[Route('/test/error/500', name: 'test_error_500')]
  public function test500(): Response
  {
    throw new \Exception('Page de test 500');
  }
}