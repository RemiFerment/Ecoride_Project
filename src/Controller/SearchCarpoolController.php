<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Carpooling;
use App\Form\SearchCarpoolType;
use App\Repository\CarpoolingRepository;
use App\Repository\ParticipationRepository;
use App\Services\GeolocationService;
use App\Services\SearchFilterPreferenceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/search/carpool')]
final class SearchCarpoolController extends AbstractController
{

    #[Route('', name: 'app_search_carpool', methods: ['GET', 'POST'])]
    public function index(Request $request, CarpoolingRepository $carpoolingRep, GeolocationService $gs, SearchFilterPreferenceService $searchFilterPreferenceService): Response
    {
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
                user: $this->getUser(),
                minGrade: $form->get('filter_grade')->getData(),
                maxPrice: $form->get('filter_price')->getData()
            );
            $preferences = $form->get('filter_preferences')->getData();

            $searchResults = $searchFilterPreferenceService->PreferenceFilter(
                filterSmoking: in_array('smoker_allowed', $preferences),
                filterAnimals: in_array('animals_allowed', $preferences),
                carpoolings: $searchResults
            );
        }
        return $this->render(
            'search_carpool/index.html.twig',
            [
                'form' => $form->createView(),
                'searchResults' => $searchResults,
                'isSubmit' => $form->isSubmitted(),
                'cities' => $_POST['search_carpool'] ?? null
            ]
        );
    }

    #[Route('/{id}', name: 'app_search_carpool_detail', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function detailCarpool(Carpooling $carpooling, ParticipationRepository $participationRep): Response
    {
        $carpoolOwner = $carpooling->getCreatedBy();
        $carpoolCar = $carpooling->getCar();

        //Vérifie si le user ne participe pas déjà au trajet.
        $isParticipate = $participationRep->findOneBy(['user' => $this->getUser(), 'carpooling' => $carpooling]) !== null;

        return $this->render('search_carpool/detail.html.twig', [
            'carpool' => $carpooling,
            'carpoolOwner' => $carpoolOwner,
            'carpoolCar' => $carpoolCar,
            'user' => $this->getUser(),
            'isParticipate' => $isParticipate
        ]);
    }

    #[Route('/api/cities', name: 'api_cities', methods: ['GET'])]
    public function getCities(Request $request, GeolocationService $geolocationService): JsonResponse
    {
        $query = $request->query->get('q', '');
        if (strlen($query) < 2) {
            return $this->json([]);
        }

        $result = $geolocationService->getCitiesFromGeonames($query);
        return $this->json($result);
    }
}
