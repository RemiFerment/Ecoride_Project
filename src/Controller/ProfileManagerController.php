<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilePictureType;
use App\Form\ProfileType;
use App\Security\Voter\ProfileManagerVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfileManagerController extends AbstractController
{
    private ?User $user;

    public function __construct(private Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/profile/manager', name: 'app_profile_manager', methods: ['GET', 'POST'])]
    #[IsGranted(ProfileManagerVoter::READ)]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(ProfilePictureType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photo')->getData();
            if ($file) {
                $stream = fopen($file->getPathname(), 'rb');
                $this->user->setPhoto($stream);
                $em->persist($this->user);
                $em->flush();
                $this->addFlash(
                    'success',
                    'La photo a bien été ajoutée !'
                );
                return $this->redirectToRoute('app_profile_manager');
            }
            $this->addFlash(
                'danger',
                'Un problème est survenue, la photo n\'a pas été ajoutée.'
            );
            return $this->redirectToRoute('app_profile_manager');
        }

        return $this->render('profile_manager/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/profile/manager/delete_photo/{id}", name: "app_delete_profile_picture", requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[IsGranted(ProfileManagerVoter::DELETE, subject: 'user')]
    public function deleteProfilePhoto(User $user, EntityManagerInterface $em)
    {
        $user->setPhoto(null);
        $em->persist($user);
        $em->flush();
        $this->addFlash(
            'success',
            'La photo a bien été supprimé !'
        );
        return $this->redirectToRoute('app_profile_manager');
    }

    #[Route("/profile/edit/{id}", name: "app_profile_edit", requirements: ['id' => '\d+'], methods: ['PUT', 'POST'])]
    #[IsGranted(ProfileManagerVoter::EDIT, subject: 'user')]
    public function editProfile(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedRole = $form->get('roles')->getData();
            // remove any existing passenger/driver roles first
            if ($selectedRole !== null) {
                $roles = $user->getRoles();
                $roles = array_values(array_diff($roles, ['ROLE_PASSAGER', 'ROLE_DRIVER']));
                $user->setRoles($roles);

                // append the selected role(s) using addRole()
                if ($selectedRole === 'twice') {
                    $user->addRole('ROLE_PASSAGER');
                    $user->addRole('ROLE_DRIVER');
                } else {
                    $user->addRole($selectedRole);
                }
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre profil a bien été mis à jour !'
            );
            return $this->redirectToRoute('app_profile_manager');
        }

        return $this->render('profile_manager/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
