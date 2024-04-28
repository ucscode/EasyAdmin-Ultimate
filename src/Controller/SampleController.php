<?php

namespace App\Controller;

use App\Controller\Admin\Abstract\AbstractAdminDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SampleController extends AbstractAdminDashboardController
{
    #[Route('/sample', name: 'app_sample')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'SampleController',
        ]);
    }
}
