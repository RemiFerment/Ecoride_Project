<?php

namespace App\Controller;

use DateTime;
use App\Services\GeolocationService;
use App\Services\GlobalStatService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(GlobalStatService $globalStatManager): Response
    {
        dd($globalStatManager->showGlobalStat(GlobalStatService::CARPOOL_STAT));
        return $this->render('test/index.html.twig', [
            'test' => $test,
            'user' => $user ?? null,
        ]);
    }
    #[Route('/test2', name: 'app_test2')]
    public function test2(GlobalStatService $globalStatManager): Response
    {
        dd($globalStatManager->incGlobalStat(GlobalStatService::CARPOOL_STAT));
        return $this->render('test/index.html.twig', [
            'test' => $test,
            'user' => $user ?? null,
        ]);
    }
}
