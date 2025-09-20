<?php

namespace App\Controller;

use App\Services\GlobalStatService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(GlobalStatService $globalStat): Response
    {
        $allCarpoolStat = $globalStat->showGlobalStat(GlobalStatService::CARPOOL_STAT) ?? "0";
        $allAccountlStat = $globalStat->showGlobalStat(GlobalStatService::ACCOUNT_STAT) ?? "0";
        $allEcopiecelStat = $globalStat->showGlobalStat(GlobalStatService::ECOPIECE_STAT) ?? "0";
        return $this->render('admin/admin.html.twig', [
            'allCarpoolStat' => $allCarpoolStat,
            'allAccountStat' => $allAccountlStat,
            'allEcopieceStat' => $allEcopiecelStat,
            'user' => $this->getUser(),
        ]);
    }
}
