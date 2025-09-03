<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilePictureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileManagerController extends AbstractController
{
    #[Route('/profile/manager', name: 'app_profile_manager')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user === null) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas accéder à cette page tant que vous n\'êtes pas connecté(e).'
            );
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ProfilePictureType::class);
        $form->handleRequest($request);

        //la photo est récupéré (au format binaire)
        $data = $user->getPhoto();
        if (is_resource($data)) {
            //On récupère l'objet binaire via la fonction ci-dessus pour pouvoir le convertir.
            $data = stream_get_contents($data);
        }
        //Conversion du flux binaire en base64
        $base64 = base64_encode($data);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $file = $form->get('photo')->getData();
            if ($file) {
                // fopen en mode rb permet d'ouvrir le fichier en mode binaire (read binary pour rb)
                $stream = fopen($file->getPathname(), 'rb');

                // le flux ouvert est donc automatiquement convertis en objet binaire sauvegardable dans un BLOB.
                $user->setPhoto($stream);

                $em->persist($user);
                $em->flush();
                $this->addFlash(
                    'success',
                    'La photo a bien été ajouté !'
                );
                return $this->redirectToRoute('app_profile_manager');
            }
            $this->addFlash(
                'danger',
                'Un problème est survenue, la photo n\'a pas été ajouté.'
            );
            return $this->redirectToRoute('app_profile_manager');
        }

        return $this->render('profile_manager/index.html.twig', [
            'user' => $user ?? null,
            'form' => $form,
            //On envoie le flux converti dans la vue, twig se chargera de l'ouvrir en png
            'base64' => $base64 ?? null
        ]);
    }
}
