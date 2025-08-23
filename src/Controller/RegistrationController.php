<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Services\JWTService;
use App\Services\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier) {}

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager,
        JWTService $jwt,
        SendEmailService $mail
    ): Response {
        if ($this->getUser()) {
            $this->addFlash(
                'warning',
                'Vous êtes déjà connecté, veuillez vous déconnecter.'
            );
            return $this->redirectToRoute('home');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

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
                'Ecoride - Confirmation de votre adresse mail',
                'register',
                compact('user', 'token') // ['user' => $user, 'token'=>$token]
            );

            $this->addFlash("success", "L'inscription a bien été effectué ! Un E-mail de confirmation vous a été envoyé.");

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route("/verif/{token}", name: "verify_user")]
    public function verifUser(
        string $token,
        JWTService $jwt,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ) {
        //Vérifier si le token est valide (cohérent, pas expiré et signature correcte)
        if ($jwt->check($token, $this->getParameter('app.jwtsecret')) && $jwt->isValid($token) && !$jwt->isExpired($token)) {

            //Ici le token est complétement valide, on récupère les données.
            $payload = $jwt->getPayload($token);

            /** @var User $user */
            $user = $userRepository->find($payload['user_id']);

            //On vérifie si User existe, et si le compte n'est pas déjà vérifié
            if ($user && !$user->isVerified()) {
                $user->setIsVerified(true);
                $em->persist($user);
                $em->flush();
            }
        }
        $this->addFlash('danger', 'Le token est invalide ou a expiré.');
        return $this->redirectToRoute('home');
    }
}
