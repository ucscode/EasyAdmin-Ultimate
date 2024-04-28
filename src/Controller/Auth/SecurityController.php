<?php

namespace App\Controller\Auth;

use App\Controller\Auth\Interface\AuthControllerInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractDashboardController implements AuthControllerInterface
{
    public function __construct(protected EntityManagerInterface $entityManager)
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
        ] + $this->easyAdminConfig());
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    protected function easyAdminConfig(): array
    {
        return [
            'page_title' => 'Uss Login',
            'favicon_path' => 'https://static.thenounproject.com/png/5265761-200.png',

            'username_label' => 'Email',
            'password_label' => 'Password',
            'sign_in_label' => 'Log in',

            'forgot_password_enabled' => true,
            'forgot_password_path' => $this->generateUrl('app_forgot_password_request'),
            'forgot_password_label' => 'Forgot your password?',

            'remember_me_enabled' => false,
            'remember_me_checked' => false,
            'remember_me_label' => 'Remember me',
        ];
    }
}
