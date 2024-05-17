<?php

namespace App\Controller\User;

use App\Controller\User\Abstracts\AbstractUserDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractUserDashboardController
{
    #[Route('/user', name: 'user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }
}
