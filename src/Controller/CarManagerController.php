<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\User;
use App\Form\CarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Requirement\Requirement;

class CarManagerController extends AbstractController
{

    #[Route('/car', name: 'app_car_index')]
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
    public function createCarpool(
        Request $request,
        EntityManagerInterface $em,
        User $user
    ): ?Response {

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
            $car->setUserId($user->getId());
            $em->persist($car);
            $em->flush();
            $user->setCurrentCarId($car->getId());
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Votre voiture à bien été ajouté !');
            return $this->redirectToRoute('app_car_index');
        }

        return $this->render('car/add.html.twig', [
            'user' => $user ?? null,
            'form' => $form
        ]);
    }

    #[Route('/car/set/{id}', requirements: ['id' => Requirement::DIGITS], name: "app_car_setDefault")]
    public function setUsedCar(EntityManagerInterface $em, CarRepository $carRep, int $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Car $car */
        $car = $carRep->find($id);
        if ($user->getId() !== $car->getUserId()) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas définir cette voiture comme principale, vérifier le lien.'
            );
            return $this->redirectToRoute('app_car_index');
        }

        $user->setCurrentCarId($car->getId());
        $em->persist($user);
        $em->flush($user);
        $this->addFlash(
            'success',
            "Votre " . $car->getMarque()->getName() . " " . $car->getModel() . " a bien été défini comme votre voiture principale."
        );
        return $this->redirectToRoute('app_car_index');
    }

    #[Route('/car/delete/{id}', requirements: ['id' => Requirement::DIGITS], name: "app_car_delete")]
    public function deleteCar(EntityManagerInterface $em, CarRepository $carRep, int $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Car $car */
        $car = $carRep->find($id);

        //Check if the car ID match with the owner of that car
        if ($user->getId() !== $car->getUserId()) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas supprimer cette voiture, vérifier le lien.'
            );
            return $this->redirectToRoute('app_car_index');
        }

        //Car is removed from the DB
        $em->remove($car);
        $em->flush();
        $allUserCar = $carRep->findAllByUserId($user->getId());

        //Check if the current deleted car is the default used car.
        if ($user->getCurrentCarId() === $id && count($allUserCar) > 0) {
            $user->setCurrentCarId($allUserCar[0]->getId());
        }
        //Check if the user haven't car yet
        if (count($allUserCar) === 0) {
            $user->setCurrentCarId(null);
        }

        $em->persist($user);
        $em->flush();

        $this->addFlash(
            'success',
            "Votre " . $car->getMarque()->getName() . " " . $car->getModel() . " a bien été supprimée."
        );

        return $this->redirectToRoute('app_car_index');
    }
}
