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

    #[Route('/carpool/{id}/participate', name: 'app_carpool_participate', requirements: ['id' => Requirement::DIGITS])]
    public function participateToCarpool(CarpoolingRepository $carpoolingRep, int $id, EntityManagerInterface $em, ParticipationRepository $participationRep)
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user === null) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas accéder à cette page tant que vous n\'êtes pas connecté.'
            );
            return $this->redirectToRoute('login');
        }
        /** @var Carpooling $carpool */
        $carpool = $carpoolingRep->find($id);

        // Vérification de chaque élément sujet à comprommetre l'acceptation au covoiturage.
        $availableSeat = $carpool->getAvailableSeat();
        if ($availableSeat === 0) {
            $this->addFlash(
                'danger',
                'Ce trajet n\'est plus disponible.'
            );
            return $this->redirectToRoute('app_carpool_index');
        }

        $price = $carpool->getPricePerPerson();
        if ($price > $user->getEcopiece()) {
            $this->addFlash(
                'danger',
                "Vous n'avez pas assez d\'écopièces ! (Écopièces nécessaire : $price.)"
            );
            return $this->redirectToRoute('app_search_carpool_detail', ['id' => $id]);
        }

        if ($participationRep->findOneBy(['user' => $user, 'carpooling' => $carpool]) !== null) {
            $this->addFlash(
                'danger',
                "Vous participez déjà à ce covoiturage."
            );
            return $this->redirectToRoute('app_search_carpool');
        }

        //On créer la participation dans le ManyToMany Participation
        $participation = new Participation();
        $participation->setUser($user);
        $participation->setCarpooling($carpool);
        $em->persist($participation);
        $em->flush();

        //On retire les écopièces du participant
        $user->setEcopiece($user->getEcopiece() - $price);
        $em->persist($user);
        $em->flush();

        // Mise à jour du nombre de place dans le covoiturage.
        $carpool->addAvailableSeat(-1);
        $em->persist($carpool);
        $em->flush();

        $cityA = $carpool->getStartPlace();
        $cityB = $carpool->getEndPlace();
        $this->addFlash(
            'success',
            "La participation au trajet $cityA > $cityB a bien été prise en compte !"
        );
        return $this->redirectToRoute('app_carpool_index');
    }
}
