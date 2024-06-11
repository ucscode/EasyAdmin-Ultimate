<?php

namespace App\Controller\Initial\Notification;

use App\Entity\User\Notification;
use App\Exceptions\AccessForbiddenException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * For information about routing condition:
 * @see https://symfony.com/doc/current/routing.html#matching-expressions
 */
class AsyncNotificationController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        //
    }

    #[Route(
        '/async/notification',
        name: 'async_notification',
        methods: 'POST',
        condition: "request.request.has('token') and request.request.has('action') and request.request.has('entityId')"
    )]
    public function index(Request $request): JsonResponse
    {
        if(!$this->isCsrfTokenValid('app-user', $request->request->get('token'))) {
            throw new InvalidCsrfTokenException();
        }

        if(!$this->getUser()) {
            throw new AccessForbiddenException('User not found');
        }

        if($request->request->get('action') !== 'read-all') {

            $entityId = $request->request->get('entityId');

            if(empty($entityId) || !is_numeric($entityId)) {
                throw new InvalidParameterException('Invalid notification identifier');
            }

            $notification = $this->entityManager->getRepository(Notification::class)->findOneBy([
                'user' => $this->getUser(),
                'id' => $entityId
            ]);

            if(!$notification) {
                throw new EntityNotFoundException('Notification entity not found');
            }

            return $this->processIndividualRequest($notification, $request);

        }

        return $this->processMarkAllRequest($request);
    }

    /**
     * Mark as read, delete or any other single action
     */
    protected function processIndividualRequest(Notification $notification, Request $request): JsonResponse
    {
        switch($request->request->get('action')) {
            case 'delete':
                $this->entityManager->remove($notification);
                break;
            default:
                $notification->setSeenByUser(true);
                $this->entityManager->persist($notification);
        }

        $this->entityManager->flush();

        return $this->clientResponse($request);
    }

    /**
     * Process bulk action of marking all notification as read
     */
    protected function processMarkAllRequest(Request $request): JsonResponse
    {
        /** @var \App\Entity\User\User */
        $user = $this->getUser();

        foreach($user->getNotifications() as $notification) {
            $notification->setSeenByUser(true);
            $this->entityManager->persist($notification);
        };

        $this->entityManager->flush();

        return $this->clientResponse($request);
    }

    protected function clientResponse(Request $request, ?string $message = null): JsonResponse
    {
        $unreadNotifications = $this->entityManager->getRepository(Notification::class)->findBy([
            'user' => $this->getUser(),
            'seenByUser' => false,
        ]);

        return $this->json([
            'count' => count($unreadNotifications),
            'action' => $request->request->get('action'),
            'message' => $message,
        ]);
    }
}
