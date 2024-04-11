<?php

namespace App\Controller\User;

use App\Controller\User\Abstract\AbstractUserDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractUserDashboardController
{
    #[Route('/user/profile', 'user_profile')]
    public function index(): Response
    {
        return $this->render('user/profile.html.twig');
    }
}