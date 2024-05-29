<?php

namespace App\Event\Listener\User;

use App\Constants\UserConstants;
use App\Entity\User\User;
use App\Service\AffiliationService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\DashboardControllerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelEventListeners
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        //
    }

    #[AsEventListener(KernelEvents::CONTROLLER, priority: -10)]
    public function updateLastSeen(ControllerEvent $event): void
    {
        /**
         * @var \EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext $adminContext
         */
        if($adminContext = $event->getRequest()->attributes->get(EA::CONTEXT_REQUEST_ATTRIBUTE)) {
            /**
             * @var ?\App\Entity\User\User
             */
            $user = $adminContext->getUser();

            if($user && in_array(DashboardControllerInterface::class, class_implements($adminContext->getDashboardControllerFqcn()))) {
                $currentTime = Carbon::now();
                $lastSeen = new Carbon($user->getLastSeen());

                /**
                 * To ensure that updates to the "last seen" information do not significantly impact the overall performance of the platform;
                 * Instead of updating the lastSeen field on every request, it can be updated periodically.
                 * For example, it might only be updated if a certain amount of time has passed since the last update (e.g., 5 minutes).
                 */
                $updateLastSeenAfterSeconds = 60 * 5; // update after every $x seconds

                /**
                 * Update the current user last seen
                 */
                if($currentTime->gt($lastSeen->addSeconds($updateLastSeenAfterSeconds))) {
                    $user->setLastSeen($currentTime);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }
            };
        }
    }
    
    #[AsEventListener(KernelEvents::RESPONSE)]
    public function saveReferralIdentityToCookie(ResponseEvent $event): void
    {
        if($referralId = $event->getRequest()->query->get(AffiliationService::QUERY_KEY)) {
            /**
             * @var User
             */
            $parent = $this->entityManager->getRepository(User::class)->findOneBy([
                'uniqueId' => $referralId
            ]);

            if($parent) {
                $cookie = new Cookie(
                    AffiliationService::COOKIE_KEY, 
                    $parent->getUniqueId(),
                    new \DateTime('+7 days')
                );
                
                $event->getResponse()->headers->setCookie($cookie);
            }
        }
    }
}
