<?php

namespace App\Controller;

use App\Document\UserPreferences;
use App\Services\MongoFilterService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(MongoFilterService $test)
    {
        $test->getUserPreferences(3);
    }
}
