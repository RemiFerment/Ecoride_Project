<?php

namespace App\Controller;

use App\Form\SearchCarpoolType;
use App\Repository\CarpoolingRepository;
use App\Services\GeolocationService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchCarpoolController extends AbstractController
{
    #[Route('/search/carpool', name: 'app_search_carpool')]
    public function index(Request $request, CarpoolingRepository $carpoolingRep, GeolocationService $gs): Response
    {
        $user = $this->getUser() ?? null;

        $searchResults = [];

        $form = $this->createForm(SearchCarpoolType::class, null, options: [
            'data' => [
                'endPlace' => $request->query->get('destination') ?? '',
            ]
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchResults = $carpoolingRep->findBySearchCarpool(
                startPlace: $gs->getOfficialCityName($form->getData()['startPlace']),
                endPlace: $gs->getOfficialCityName($form->getData()['endPlace']),
                date: new DateTimeImmutable(($form->getData()['startDateTime'])->format('Y-m-d')),
                user: $user
            );
        }

        return $this->render(
            'search_carpool/index.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
                'searchResults' => $searchResults
            ]
        );
    }
}
