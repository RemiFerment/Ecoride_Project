<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DriverDetailsController extends AbstractController
{
    #[Route('/driver/details/{id}', name: 'app_driver_details', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function DriverDetails(UserRepository $user_repository, int $id, DocumentManager $document_manager): Response
    {
        /** @var User $driver */
        $driver = $user_repository->find($id);
        if (!$driver) {
            $this->addFlash(
                'error',
                'L\'utlisateur est introuvable'
            );
            return $this->redirectToRoute('home');
        }

        $preference = $document_manager->getRepository('App\Document\UserPreferences')->findOneBy(['userId' => (string)$driver->getId()]);

        return $this->render('driver_details/driver_details.html.twig', [
            'driver' => $driver,
            'preference' => $preference,
        ]);
    }
}
