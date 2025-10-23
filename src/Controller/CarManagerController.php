<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\User;
use App\Form\CarType;
use App\Repository\CarRepository;
use App\Security\Voter\CarVoter;
use App\Services\CarManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/car')]
class CarManagerController extends AbstractController
{
    private ?User $user;

    public function __construct(private Security $security)
    {
        $this->user = $this->security->getUser();
    }

    #[Route('', name: 'app_car_index', methods: ['GET'])]
    #[IsGranted(CarVoter::READ)]
    public function index(CarRepository $carRepository): Response
    {
        $carArray = $carRepository->findAllByUserId($this->user->getId());

        //Affiche la liste des covoiturages prévu et déjà effectué.
        return $this->render('car/index.html.twig', [
            'cars' => $carArray ?? null
        ]);
    }

    #[Route('/add', name: 'app_car_add', methods: ['GET', 'POST'])]
    #[IsGranted(CarVoter::CREATE)]
    public function createCar(Request $request, CarManagerService $carManager): Response
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Car $car */
            $car = $form->getData();

            $carManager->FinalizeCreate($this->user, $car);

            $this->addFlash('success', 'Votre voiture à bien été ajouté !');
            return $this->redirectToRoute('app_car_index');
        }

        return $this->render('car/add.html.twig', [
            'edit' => false,
            'form' => $form->createView()
        ]);
    }

    #[Route('/set/{id}', requirements: ['id' => Requirement::DIGITS], name: "app_car_setDefault", methods: ['POST'])]
    #[IsGranted(CarVoter::UPDATE, subject: 'car')]
    public function setUsedCar(Car $car, CarManagerService $carManager): Response
    {
        $carManager->FinalizeSetDefaultCar($this->user, $car);

        $this->addFlash(
            'success',
            "Votre " . $car->getMarque()->getName() . " " . $car->getModel() . " a bien été défini comme votre voiture principale."
        );
        return $this->redirectToRoute('app_car_index');
    }

    #[Route('/delete/{id}', requirements: ['id' => Requirement::DIGITS], name: "app_car_delete", methods: ['DELETE'])]
    #[IsGranted(CarVoter::DELETE, subject: 'car')]
    public function deleteCar(Car $car, CarManagerService $carManager, CarRepository $carRep): Response
    {
        $carManager->FinalizeDeletion($this->user, $car);

        $allUserCar = $carRep->findAllByUserId($this->user->getId());

        $newCar = $this->user->getCurrentCar() === null && count($allUserCar) > 0 ? $allUserCar[0] : null;

        $carManager->FinalizeSetDefaultCar($this->user, $newCar);

        $this->addFlash(
            'success',
            "Votre " . $car->getMarque()->getName() . " " . $car->getModel() . " a bien été supprimée."
        );

        return $this->redirectToRoute('app_car_index');
    }

    #[Route('/edit/{id}', requirements: ['id' => Requirement::DIGITS], name: "app_car_edit", methods: ['GET', 'POST'])]
    #[IsGranted(CarVoter::UPDATE, subject: 'car')]
    public function editCar(Car $car, Request $request, CarManagerService $carManager): Response
    {
        $form = $this->createForm(CarType::class, $car, options: ['edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Car $car */
            $car = $form->getData();

            $carManager->FinalizeCreate($this->user, $car, edit: true);

            $this->addFlash('success', 'Votre voiture à bien été modifiée !');
            return $this->redirectToRoute('app_car_index');
        }

        return $this->render('car/add.html.twig', [
            'edit' => true,
            'form' => $form->createView()
        ]);
    }
}
