<?php

namespace App\Controller\Auth;

use App\Controller\Auth\Abstracts\AbstractAuth;
use App\Service\ConfigurationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractAuth
{
    public function __construct(protected ConfigurationService $configurationService)
    {
        
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [

            'last_username' => $lastUsername,
            'error' => $error,
            
            'csrf_token_intention' => 'authenticate',

            'page_title' => sprintf('%s | %s', $this->configurationService->getConfigurationValue('app.name'), 'Login'),
            'favicon_path' => $this->getConfigurationLogo('https://static.thenounproject.com/png/5265761-200.png'),

            'header_title' => 'Login Now',
            'header_logo' => $this->getConfigurationLogo(),

            'username_label' => 'Email',
            'password_label' => 'Password',
            'sign_in_label' => 'Log in',

            'forgot_password_enabled' => true,
            'forgot_password_path' => $this->generateUrl('app_forgot_password_request'),
            'forgot_password_label' => 'Forgot your password?',

            'remember_me_enabled' => false,
            'remember_me_checked' => false,
            'remember_me_label' => 'Remember me',

        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
