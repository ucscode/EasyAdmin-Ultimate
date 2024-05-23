<?php

namespace App\Controller\Initial\Legality;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TermsAndConditionController extends AbstractController
{
    #[Route('/terms-and-conditions', name: 'terms_conditions')]
    public function termsOfService(): Response
    {
        return $this->render('initial/terms_and_condition.html.twig');
    }
}
