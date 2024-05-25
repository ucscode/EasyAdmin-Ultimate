<?php

namespace App\Event\Subscriber;

use App\Service\ConfigurationService;
use App\Utils\RoleUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Ucscode\KeyGenerator\KeyGenerator;

/**
 * A subscriber that listens to log in authentication events
 *
 * Login requests are (by default) processed by symfony Login form authenticator.
 * However, you can create your own authenticator if you need absolute control.
 *
 * Alternatively, you can take advantage of dispatched events during authentication processes.
 * The FormLoginAuthenticator dispatches several events during the authentication process
 * By listening to these events, you can control the authentication process without necessarily
 * creating a custom login authenticator.
 *
 * References
 *
 * @see \Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
 *
 * @link https://symfony.com/doc/4.x/security/form_login_setup.html
 * @link https://symfony.com/doc/current/security.html#form-login
 * @link https://symfonycasts.com/screencast/symfony-security/security-listeners
 */
class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected RequestStack $requestStack,
        protected ConfigurationService $configurationService,
        protected UrlGeneratorInterface $urlGenerator,
    ) {

    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10],
            LoginSuccessEvent::class => ['onLoginSuccess', 1],
        ];
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $passport = $event->getPassport();

        /**
         * @var \App\Entity\User\User
         */
        $user = $passport->getUser();

        if($this->configurationService->get('user.account.login_only_if_verified')) {

            if(!$user->getIsVerified()) {
                /**
                 * @var \Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface
                 */
                $session = $this->requestStack->getCurrentRequest()->getSession();
                $token = (new KeyGenerator())->generateKey(32);

                $verificationMessage = sprintf(
                    "If you did not receive a confirmation email previously, <a href='%s'>request a new one</a>",
                    $this->urlGenerator->generate('app_login_email_reconfirm', [
                        'token' => $token,
                    ])
                );

                $session->set('user.email.token', $token); // set session
                $session->getFlashBag()->add('info.is_unverified', $verificationMessage); // add flash message

                throw new CustomUserMessageAuthenticationException('Please verify your account email to login');
            }

        }
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        /**
         * @var \App\Entity\User\User
         */
        $user = $event->getAuthenticatedToken()->getUser();

        // default redirect path
        $response = new RedirectResponse($this->urlGenerator->generate('app_user'));

        $redirections = [
            RoleUtils::ROLE_ADMIN => 'app_admin',
            // add more redirection path
        ];

        foreach($redirections as $role => $pathname) {
            if($user->hasRole($role)) {
                $response = new RedirectResponse($this->urlGenerator->generate($pathname));
                break;
            }
        }

        $event->setResponse($response);
    }
}
