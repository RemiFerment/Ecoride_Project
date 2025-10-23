<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/moderator')]
final class ModeratorController extends AbstractController
{
    #[Route('/dashboard', name: 'app_moderator')]
    public function index(ReviewRepository $reviewRepository)
    {
        //dashboard dans laquelle il y a deux choix : Un pour vérifié les avis, un pour gérer les 
        //Donc ici, il faut juste que je récupère le nombre d'avis à checker + le nombre d'avis dont la note est inférieur à 3

    }
}
