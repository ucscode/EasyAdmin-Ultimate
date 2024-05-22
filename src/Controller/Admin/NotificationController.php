<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NotificationController extends AbstractDashboardController
{
    #[Route('/admin/info/notification', 'admin_notification')]
    public function index(): Response
    {
        return $this->render('initial/notification.html.twig', [
            
        ]);
    }
}