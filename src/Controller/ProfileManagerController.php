<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilePictureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfileManagerController extends AbstractController
{
    #[Route('/profile/manager', name: 'app_profile_manager')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
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
            'form' => $form->createView(),
        ]);
    }

    #[Route("/profile/manager/{user_id}/delete_photo", name: "app_delete_profile_picture", requirements: ['user_id' => '\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteProfilePhoto(EntityManagerInterface $em, int $user_id)
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user_id !== $user->getId()) {
            $this->addFlash(
                'danger',
                'Le lien que vous avez sélectionné ne vous est pas destiné, vérifiez le lien.'
            );
            return $this->redirectToRoute('app_profile_manager');
        }
        $user->setPhoto(null);
        $em->persist($user);
        $em->flush();
        $this->addFlash(
            'success',
            'La photo a bien été supprimé !'
        );
        return $this->redirectToRoute('app_profile_manager');
    }
}
