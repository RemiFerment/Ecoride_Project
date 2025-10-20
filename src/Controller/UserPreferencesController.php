<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPreferencesType;
use App\Document\UserPreferences;
use App\Repository\UserPreferencesRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserPreferencesController extends AbstractController
{
    #[Route('/user/preferences', name: 'app_user_preferences')]
    public function preferenceSettings(Request $request, DocumentManager $documentManager): Response
    {
        /** @var User */
        $user = $this->getUser();

        /** @var UserPreferencesRepository */
        $userPreferencesRepo = $documentManager->getRepository(UserPreferences::class);

        /** @var UserPreferences */
        $preferences = $userPreferencesRepo->findByUserId($user->getId());

        if (!$preferences) {
            $preferences = new UserPreferences();
            $preferences->setUserId($user->getId());
        }

        $form = $this->createForm(UserPreferencesType::class, $preferences);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documentManager->persist($preferences);
            $documentManager->flush();

            $this->addFlash('success', 'Vos préférences ont été enregistrées avec succès.');
            return $this->redirectToRoute('app_user_preferences');
        }

        return $this->render('user_preferences/index.html.twig', [
            'form' => $form->createView(),
            'preferences' => $preferences,
        ]);
    }
}
