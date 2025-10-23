<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPreferencesType;
use App\Document\UserPreferences;
use App\Repository\UserPreferencesRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserPreferencesController extends AbstractController
{
    private User $user;
    public function __construct(private Security $security)
    {
        $this->user = $this->security->getUser();
    }

    #[Route('/user/preferences', name: 'app_user_preferences')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[IsGranted('ROLE_DRIVER')]
    public function preferenceSettings(Request $request, DocumentManager $documentManager): Response
    {


        /** @var UserPreferencesRepository */
        $userPreferencesRepo = $documentManager->getRepository(UserPreferences::class);

        /** @var UserPreferences */
        $preferences = $userPreferencesRepo->findByUserId($this->user->getId());

        if (!$preferences) {
            $preferences = new UserPreferences();
            $preferences->setUserId($this->user->getId());
        }

        $form = $this->createForm(UserPreferencesType::class, $preferences);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documentManager->persist($preferences);
            $documentManager->flush();

            $this->addFlash('success', 'Vos préférences ont été enregistrées avec succès.');
            return $this->redirectToRoute('app_profile_manager');
        }

        return $this->render('user_preferences/index.html.twig', [
            'form' => $form->createView(),
            'preferences' => $preferences,
        ]);
    }
}
