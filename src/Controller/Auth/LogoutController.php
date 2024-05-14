<?php

namespace App\Controller\Auth;

use App\Controller\Auth\Abstracts\AbstractAuth;
use Symfony\Component\Routing\Attribute\Route;

class LogoutController extends AbstractAuth
{
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
