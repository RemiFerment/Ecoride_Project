<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    #[IsGranted('ROLE_ADMIN')]
    public function index()
    {
        return $this->render('test/index.html.twig', []);
    }
}
