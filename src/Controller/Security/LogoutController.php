<?php

namespace App\Controller\Security;

use App\Controller\Security\Abstracts\AbstractSecurityController;
use Symfony\Component\Routing\Attribute\Route;

class LogoutController extends AbstractSecurityController
{
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
