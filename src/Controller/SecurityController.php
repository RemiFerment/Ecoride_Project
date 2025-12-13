<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPaswwordType;
use App\Security\Voter\RegistrationVoter;
use App\Services\JWTService;
use App\Services\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    #[IsGranted(RegistrationVoter::VIEW)]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        $this->addFlash('danger', 'Vous vous êtes déconnecté.');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/forgot-password-form', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request, EntityManagerInterface $em, SendEmailService $mail, JWTService $jwt): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ForgotPaswwordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $email = $formData['Email'];
            if ($em->getRepository('App\Entity\User')->findOneBy(['email' => $email])) {
                //Générer le token 
                //Header
                $header = [
                    'typ' => 'JWT',
                    'alg' => 'HS256'
                ];
                //Payload
                $payload = [
                    'user_id' => $user->getId()
                ];

                $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

                //Envoyer l'email.
                $mail->send(
                    'no-reply@ecoride-project.test',
                    $user->getEmail(),
                    'Ecoride - Réinitialisation de votre mot de passe',
                    'register',
                    compact('user', 'token')
                );
            }

            $this->addFlash('success', 'Si un compte avec cette adresse email existe, un email de réinitialisation a été envoyé.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(string $token): Response
    {
        return $this->render('security/reset_password.html.twig', [
            'token' => $token,
        ]);
    }
}
