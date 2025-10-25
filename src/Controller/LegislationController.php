<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegislationController extends AbstractController
{
    #[Route('/mentions-legales', name: 'app_legislation')]
    public function index(): Response
    {
        return $this->render('legislation/index.html.twig');
    }
}
