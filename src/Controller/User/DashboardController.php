<?php

namespace App\Controller\User;

use App\Controller\User\Abstracts\AbstractUserDashboardController;
use App\Service\AffiliationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractUserDashboardController
{
    public function __construct(protected AffiliationService $affiliationService)
    {
        
    }

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'reflink' => $this->affiliationService->getReferralLink($this->getUser()),
        ]);
    }
}
