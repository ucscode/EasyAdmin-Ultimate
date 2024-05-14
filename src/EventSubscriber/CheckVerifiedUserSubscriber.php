<?php

namespace App\EventSubscriber;

use App\Service\ConfigurationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Ucscode\KeyGenerator\KeyGenerator;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
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
        ];
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $passport = $event->getPassport();

        /**
         * @var \App\Entity\User
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
}
