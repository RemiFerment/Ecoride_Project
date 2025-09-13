<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class WalletController extends AbstractController
{
    #[Route('/wallet', name: 'app_wallet')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('wallet/index.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/wallet/addfund', name: 'app_wallet_addfund')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function addFund()
    {
        $user = $this->getUser();
        return $this->render('wallet/addfun.html.twig', [
            'user' => $user
        ]);
    }
}
