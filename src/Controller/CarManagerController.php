<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\User;
use App\Form\CarType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CarManagerController extends AbstractController
{

    #[Route('/cars', name: 'app_car_index')]
    public function index()
    {
        $user = $this->getUser();
        if ($user === null) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas accéder à cette page tant que vous n\'êtes pas connecté(e).'
            );
            return $this->redirectToRoute('home');
        }
        //Affiche la liste des covoiturages prévu et déjà effectué.
        return $this->render('cars/index.html.twig', [
            'user' => $user ?? null
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
            $em->persist($car);
            $em->flush();
            $user->setCurrentCarId($car->getId());
            $this->addFlash('success', 'Votre voiture à bien été ajouté !');
            return $this->redirectToRoute('app_car_index');
        }

        return $this->render('cars/add.html.twig', [
            'user' => $user ?? null,
            'form' => $form
        ]);
    }
}
