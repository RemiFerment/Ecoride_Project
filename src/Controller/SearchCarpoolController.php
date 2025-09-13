<?php

namespace App\Controller;

use App\Entity\Carpooling;
use App\Entity\Participation;
use App\Entity\User;
use App\Form\SearchCarpoolType;
use App\Repository\CarpoolingRepository;
use App\Repository\ParticipationRepository;
use App\Services\GeolocationService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
                'searchResults' => $searchResults,
                'isSubmit' => $form->isSubmitted(),
                'cities' => $_POST['search_carpool'] ?? null
            ]
        );
    }

    #[Route('/search/carpool/{id}/detail', name: 'app_search_carpool_detail', requirements: ['id' => Requirement::DIGITS])]
    public function detailCarpool(CarpoolingRepository $carpoolingRep, int $id, Carpooling $carpool, ParticipationRepository $participationRep): Response
    {
        $user = $this->getUser();
        /** @var Carpooling $carpool */
        $carpool = $carpoolingRep->find($id);
        $carpoolOwner = $carpool->getCreatedBy();
        $carpoolCar = $carpool->getCar();

        //Vérifie si le user ne participe pas déjà au trajet.
        $isParticipate = $participationRep->findOneBy(['user' => $user, 'carpooling' => $carpool]) !== null;

        return $this->render('search_carpool/detail.html.twig', [
            'carpool' => $carpool,
            'carpoolOwner' => $carpoolOwner,
            'carpoolCar' => $carpoolCar,
            'user' => $user,
            'isParticipate' => $isParticipate
        ]);
    }

   
}
