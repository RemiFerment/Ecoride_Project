<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\User;
use App\Form\CarType;
use App\Repository\CarpoolingRepository;
use App\Repository\CarRepository;
use App\Services\CarManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CarManagerController extends AbstractController
{

    #[Route('/car', name: 'app_car_index')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[IsGranted('ROLE_DRIVER')]
    public function index(CarRepository $carRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user === null) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas accéder à cette page tant que vous n\'êtes pas connecté(e).'
            );
            return $this->redirectToRoute('home');
        }
        $carArray = $carRepository->findAllByUserId($user->getId());
        // dd($carArray);
        //Affiche la liste des covoiturages prévu et déjà effectué.
        return $this->render('car/index.html.twig', [
            'user' => $user ?? null,
            'cars' => $carArray ?? null
        ]);
    }

    #[Route('/car/add', name: 'app_car_add')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[IsGranted('ROLE_DRIVER')]
    public function createCar(Request $request, CarManagerService $carManager): ?Response
    {

        /** @var User $user  */
        $user = $this->getUser();
        if ($user === null) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas accéder à cette page tant que vous n\'êtes pas connecté.'
            );
            return $this->redirectToRoute('home');
        }
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Car $car */
            $car = $form->getData();

            $carManager->FinalizeCreate($user, $car);

            $this->addFlash('success', 'Votre voiture à bien été ajouté !');
            return $this->redirectToRoute('app_car_index');
        }

        return $this->render('car/add.html.twig', [
            'user' => $user ?? null,
            'form' => $form->createView()
        ]);
    }

    #[Route('/car/set/{id}', requirements: ['id' => Requirement::DIGITS], name: "app_car_setDefault")]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[IsGranted('ROLE_DRIVER')]
    public function setUsedCar(int $id, CarManagerService $carManager, CarRepository $carRep): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Car $car */
        $car = $carRep->find($id);

        $carManager->FinalizeSetDefaultCar($user, $car);

        $this->addFlash(
            'success',
            "Votre " . $car->getMarque()->getName() . " " . $car->getModel() . " a bien été défini comme votre voiture principale."
        );
        return $this->redirectToRoute('app_car_index');
    }

    #[Route('/car/delete/{id}', requirements: ['id' => Requirement::DIGITS], name: "app_car_delete")]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[IsGranted('ROLE_DRIVER')]
    public function deleteCar(CarManagerService $carManager, CarRepository $carRep, CarpoolingRepository $carpoolingRepository, int $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Car $car */
        $car = $carRep->find($id);

        if ($user->getId() !== $car->getUserId()) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas supprimer cette voiture, vérifier le lien.'
            );
            return $this->redirectToRoute('app_car_index');
        }

        if ($carpoolingRepository->findCarpoolByUserAndCar($car, $user)) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas supprimer cette voiture car elle est utilisé pour effectuer un covoiturage.'
            );
            return $this->redirectToRoute('app_car_index');
        }

        $carManager->FinalizeDelation($user, $car);

        $allUserCar = $carRep->findAllByUserId($user->getId());

        $newCar = $user->getCurrentCar() === null && count($allUserCar) > 0 ? $allUserCar[0] : null;

        $carManager->FinalizeSetDefaultCar($user, $newCar);

        $this->addFlash(
            'success',
            "Votre " . $car->getMarque()->getName() . " " . $car->getModel() . " a bien été supprimée."
        );

        return $this->redirectToRoute('app_car_index');
    }
}
