<?php

namespace App\Controller;

use App\Document\CarpoolPerDayStat;
use App\Document\EcopiecePerDayStat;
use App\Entity\User;
use App\Form\EmployeeType;
use App\Services\GlobalStatService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    #[Route('/user-list', name: 'app_admin_user_list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function userList(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();
        return $this->render('admin/user_list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user-delete/{id}', name: 'app_admin_user_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        if ($this->getUser() === $user) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('app_admin_user_list');
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        return $this->redirectToRoute('app_admin_user_list');
    }

    #[Route('/api/admin/stats/carpools-per-day', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function carpoolsPerDay(DocumentManager $dm): JsonResponse
    {
        $collection = $dm->getDocumentCollection(CarpoolPerDayStat::class);

        $data = $collection->aggregate([
            [
                '$group' => [
                    '_id' => [
                        '$dateToString' => [
                            'format' => '%Y-%m-%d',
                            'date' => '$date'
                        ]
                    ],
                    'count' => ['$sum' => '$carpoolsLaunch']
                ]
            ],
            ['$sort' => ['_id' => 1]]
        ])->toArray();

        return $this->json($data);
    }

    #[Route('/api/admin/stats/ecopieces-per-day', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function ecopiecesPerDay(DocumentManager $dm): JsonResponse
    {
        $collection = $dm->getDocumentCollection(EcopiecePerDayStat::class);

        $data = $collection->aggregate([
            [
                '$group' => [
                    '_id' => [
                        '$dateToString' => [
                            'format' => '%Y-%m-%d',
                            'date' => '$date'
                        ]
                    ],
                    'count' => ['$sum' => '$ecopieces']
                ]
            ],
            ['$sort' => ['_id' => 1]]
        ])->toArray();

        return $this->json($data);
    }
}
