<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EmployeeType;
use App\Services\GlobalStatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(GlobalStatService $globalStat): Response
    {
        $allCarpoolStat = $globalStat->showGlobalStat(GlobalStatService::CARPOOL_STAT) ?? "0";
        $allAccountlStat = $globalStat->showGlobalStat(GlobalStatService::ACCOUNT_STAT) ?? "0";
        $allEcopiecelStat = $globalStat->showGlobalStat(GlobalStatService::ECOPIECE_STAT) ?? "0";
        return $this->render('admin/admin.html.twig', [
            'allCarpoolStat' => $allCarpoolStat,
            'allAccountStat' => $allAccountlStat,
            'allEcopieceStat' => $allEcopiecelStat,
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/create-employee', name: 'app_admin_create_employee', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createEmployee(Request $request, EntityManagerInterface $em): Response
    {
        $employee = new User();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employee
                ->setRoles(['ROLE_MODERATOR'])
                ->setIsVerified(true)
                ->setPhoneNumber('')
                ->setEcopiece(0)
                ->setPassword(password_hash($form->get('plainPassword')->getData(), PASSWORD_BCRYPT))
                ->setPostalAdress('N/A')
                ->setGrade(5);
            $em->persist($employee);
            $em->flush();
            $this->addFlash('success', 'Le modérateur a été créé avec succès.');
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/create_employee.html.twig', [
            'employeeForm' => $form->createView(),
        ]);
    }
}
