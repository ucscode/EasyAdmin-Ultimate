<?php

namespace App\EventSubscriber;

use App\Service\ConfigurationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
    public function __construct(protected ConfigurationService $configurationService)
    {

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
                throw new CustomUserMessageAuthenticationException(
                    'Please verify your account email to login'
                );
            }
        }
    }
}
