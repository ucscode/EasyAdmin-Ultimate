<?php

namespace App\Controller\Initial\Notification;

use App\Controller\Initial\Abstracts\AbstractInitialDashboardController;
use App\Entity\User\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Learn more about doctrine pagination in symfony here
 * @see https://symfonycasts.com/screencast/symfony-doctrine/pagination
 * 
 * @author Ucscode
 */
class NotificationController extends AbstractInitialDashboardController
{
    public const ROUTE_NAME = 'app_notification';

    public function __construct(protected EntityManagerInterface $entityManager)
    {
        
    }

    #[Route('/app/notification', self::ROUTE_NAME)]
    public function notification(Request $request): Response
    {
        if($request->attributes->get('_route') === self::ROUTE_NAME) {
            throw $this->createAccessDeniedException('Direct access to this page is not allowed!');
        }
        
        $queryBuilder = $this->entityManager->getRepository(Notification::class)
            ->createQueryBuilder('N')
            ->where('N.user = :user')
            ->orderBy('N.id', 'DESC')
            ->setParameter('user', $this->getUser())
        ;

        $adapter = new QueryAdapter($queryBuilder);
        $pager = new Pagerfanta($adapter);

        $pager
            ->setMaxPerPage(8)
            ->setCurrentPage($request->query->get('page', 1))
        ;

        return $this->render("initial/notification.html.twig", [
            'pager' => $pager,
        ]);
    }
}