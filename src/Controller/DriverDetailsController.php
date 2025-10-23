<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DriverDetailsController extends AbstractController
{
    #[Route('/driver/details/{id}', name: 'app_driver_details', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function DriverDetails(User $user, DocumentManager $document_manager): Response
    {
        if (!$user) {
            $this->addFlash(
                'error',
                'L\'utlisateur est introuvable'
            );
            return $this->redirectToRoute('home');
        }

        $preference = $document_manager->getRepository('App\Document\UserPreferences')->findOneBy(['userId' => (string)$user->getId()]);

        return $this->render('driver_details/driver_details.html.twig', [
            'driver' => $user,
            'preference' => $preference,
        ]);
    }
}
