<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DriverDetailsController extends AbstractController
{
    #[Route('/driver/details', name: 'app_driver_details')]
    public function index(): Response
    {
        return $this->render('driver_details/index.html.twig', [
            'controller_name' => 'DriverDetailsController',
        ]);
    }
}
