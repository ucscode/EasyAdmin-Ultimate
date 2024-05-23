<?php

namespace App\Controller\Initial\Legality;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PrivacyPolicyController extends AbstractController
{
    #[Route('/privacy-policy', name: 'privacy_policy')]
    public function privacyPolicy(): Response
    {
        return $this->render('initial/privacy_policy.html.twig');
    }
}
