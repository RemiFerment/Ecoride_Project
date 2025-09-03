<?php

namespace App\Controller;

use App\Services\GeolocationService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(GeolocationService $geoloc): Response
    {
        $test = new DateTime();
        return $this->render('test/index.html.twig', [
            'test' => $test,
            'user' => $user ?? null,
        ]);
    }
}
