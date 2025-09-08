<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WalletController extends AbstractController
{
    #[Route('/wallet', name: 'app_wallet')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('wallet/index.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/wallet/addfund', name: 'app_wallet_addfund')]
    public function addFund()
    {
        $user = $this->getUser();
        return $this->render('wallet/addfun.html.twig', [
            'user' => $user
        ]);
    }
}
