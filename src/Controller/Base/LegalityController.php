<?php

namespace App\Controller\Base;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LegalityController extends AbstractController
{
    #[Route('/terms-and-conditions', name: 'terms_conditions')]
    public function termsOfService(): Response
    {
        // return $this->render('your-terms-template.html.twig');
        return new Response('Info: please render a template to write your <strong>terms and condition</strong>');
    }

    #[Route('/privacy-policy', name: 'privacy_policy')]
    public function privacyPolicy(): Response
    {
        // return $this->render('your-privacy-template.html.twig');
        return new Response('Info: please render a template to write your <strong>privacy policy</strong>');
    }
}