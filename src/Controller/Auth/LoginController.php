<?php

namespace App\Controller\Auth;

use App\Controller\Auth\Abstracts\AbstractAuth;
use App\Model\BsModal\BsModal;
use App\Model\BsModal\BsModalButton;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\ConfigurationService;
use App\Service\ModalService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractAuth
{
    protected const EMAIL_VERIFICATION_ERROR = 'Verification email could not be sent.';
    protected const EMAIL_TOKEN_ERROR = 'Authentication token has changed or expired.';
    
    public function __construct(
        protected ConfigurationService $configurationService, 
        protected EmailVerifier $emailVerifier,
        protected RequestStack $requestStack,
        protected ModalService $modalService
    )
    {

    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('security/login.html.twig', [

            'last_username' => $lastUsername,
            'error' => $error,
            'is_unverified' => false,

            'csrf_token_intention' => 'authenticate',

            'page_title' => sprintf('%s | %s', $this->configurationService->get('app.name'), 'Login'),
            'favicon_path' => $this->configurationService->get('app.logo', $this->favicon),

            'header_title' => 'Login Now',
            'header_logo' => $this->configurationService->get('app.logo'),

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

    #[Route('/login/email/reconfirm', name: 'app_login_email_reconfirm')]
    public function verifyEmail(Request $request, AuthenticationUtils $authenticationUtils, UserRepository $userRepository): Response
    {
        /**
         * @var \Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface
         */
        $session = $request->getSession();

        try {

            $lastUsername = $authenticationUtils->getLastUsername();
            $token = $request->query->get('token');

            if(empty($lastUsername) || empty($token)) {
                throw new AuthenticationCredentialsNotFoundException();
            }

            if($session->get('user.email.token') !== $token) {
                throw new CustomUserMessageAuthenticationException(self::EMAIL_TOKEN_ERROR);
            }

            $session->remove('user.email.token');
            
            /**
             * @var null|\App\Entity\User
             */
            $user = $userRepository->loadUserByIdentifier($lastUsername);

            if(!$user) {
                throw new CustomUserMessageAuthenticationException(self::EMAIL_VERIFICATION_ERROR);
            }

            $this->emailVerifier->sendRegistrationVerificationEmail($user);
            
            $session->getFlashBag()->add('success.email_reconfirm', 'A verification link has been sent to your email');

        } catch (AuthenticationException $e) {
            $session->getFlashBag()->add('danger.email_reconfirm', $e->getMessageKey());
        }

        return $this->redirectToRoute('app_login');
    }
}
