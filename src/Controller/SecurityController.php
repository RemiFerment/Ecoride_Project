<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPaswwordType;
use App\Form\ResetPasswordType;
use App\Security\Voter\RegistrationVoter;
use App\Services\JWTService;
use App\Services\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Role\Role;
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
        $form = $this->createForm(ForgotPaswwordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $email = $formData['email'];
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user) {
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
                    'forgot_password',
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
    public function resetPassword(string $token, JWTService $jwt, Request $request, EntityManagerInterface $em): Response
    {
        if (!$jwt->isValid($token)) {
            $this->addFlash('danger', 'Le lien a expiré ou est invalide.');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $payload = $jwt->getPayload($token);
            $userId = $payload['user_id'];
            /** @var User */
            $user = $em->getRepository(User::class)->find($userId);

            if (!$user) {
                $this->addFlash('danger', 'Le lien a expiré ou est invalide.');
                return $this->redirectToRoute('app_login');
            }

            $formData = $form->getData();
            $newPassword = $formData['password'];
            $confirmPassword = $formData['confirm_password'];
            if ($newPassword !== $confirmPassword) {
                $this->addFlash('danger', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_reset_password', ['token' => $token]);
            }
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/', $newPassword)) {
                $this->addFlash('danger', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un caractère spécial.');
                return $this->redirectToRoute('app_reset_password', ['token' => $token]);
            }
            $user->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));
            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/reset_password.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reset-password', name: 'app_reset_password_no_token', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function resetPasswordNoToken(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User */
        $user = $this->getUser();
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();
            $newPassword = $formData['password'];
            $confirmPassword = $formData['confirm_password'];
            if ($newPassword !== $confirmPassword) {
                $this->addFlash('danger', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_reset_password_no_token');
            }
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/', $newPassword)) {
                $this->addFlash('danger', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un caractère spécial.');
                return $this->redirectToRoute('app_reset_password_no_token');
            }
            $user->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));
            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
            return $this->redirectToRoute('home');
        }
        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
