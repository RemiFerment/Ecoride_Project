<?php

namespace App\Controller;

use App\Repository\CarRepository;
use DateTime;
use App\Services\GeolocationService;
use App\Services\GlobalStatService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(CarRepository $carRepository): Response
    {
        //get all cars from a user_id 
        /** @var User $user */
        $user = $this->getUser();
        $cars = $carRepository->findBy(['user_id' => $user->getId()]);
        return $this->render('test/index.html.twig', [
            'user' => $user ?? null,
            'cars' => $cars,
        ]);
    }
}
