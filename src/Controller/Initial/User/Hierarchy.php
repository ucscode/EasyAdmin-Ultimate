<?php

namespace App\Controller\Initial\User;

use App\Controller\Initial\Abstracts\AbstractInitialDashboardController;
use App\Entity\User\User;
use App\Exceptions\AccessForbiddenException;
use App\Service\AffiliationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Hierarchy extends AbstractInitialDashboardController
{
    public const ROUTE_NAME = 'app_user_hierarchy';
    
    public function __construct(protected EntityManagerInterface $entityManager, protected AffiliationService $affiliationService)
    {
        
    }

    #[Route("/hierarchy", name: self::ROUTE_NAME)]
    public function familyTree(Request $request): Response
    {
        if($request->attributes->get('_route') === self::ROUTE_NAME) {
            throw new AccessForbiddenException();
        }

        $parameters = new ParameterBag($request->query->all('routeParams'));
        
        $target = $this->getUser();

        if($parameters->get('entityId')) {
            /**
             * @var User
             */
            $target = $this->entityManager->getRepository(User::class)->find($parameters->get('entityId'));
        }

        $ancestors = $this->affiliationService->hasAncestor(164, $target);

        // dd($children->fetchAllAssociative());

        return $this->render('initial/user_hierarchy.html.twig', [

        ]);
    }
}