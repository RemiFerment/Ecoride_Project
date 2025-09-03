<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchCarpoolController extends AbstractController
{
    #[Route('/search/carpool', name: 'app_search_carpool')]
    public function index(): Response
    {
        return $this->render('search_carpool/index.html.twig', [
            'controller_name' => 'SearchCarpoolController',
        ]);
    }
}
