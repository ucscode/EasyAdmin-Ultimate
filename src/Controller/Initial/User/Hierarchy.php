<?php

namespace App\Controller\Initial\User;

use App\Controller\Initial\Abstracts\AbstractInitialDashboardController;
use App\Entity\User\User;
use App\Exceptions\AccessForbiddenException;
use App\Utility\Table\ColumnCell;
use App\Service\AffiliationService;
use App\Utility\Table\DataCell;
use App\Utility\Table\TableBuilder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Hierarchy extends AbstractInitialDashboardController
{
    public const ROUTE_NAME = 'app_user_hierarchy';

    protected ParameterBag $parameters;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected AffiliationService $affiliationService,
        protected AdminUrlGenerator $adminUrlGenerator,
    ) {

    }

    #[Route("/hierarchy", name: self::ROUTE_NAME)]
    public function familyTree(Request $request): Response
    {
        if($request->attributes->get('_route') === self::ROUTE_NAME) {
            throw new AccessForbiddenException();
        }

        if(!$this->affiliationService->isEnabled()) {
            throw new AccessForbiddenException('You are not allowed to access this page');
        }

        $this->parameters = new ParameterBag($request->query->all('routeParams'));

        $nodeEntity = $this->parameters->get('entityId') ?
            $this->entityManager->getRepository(User::class)->find($this->parameters->get('entityId')) :
            $this->getUser();

        $structure = $this->getGenealogyStructure($nodeEntity, $this->getUser());

        $table = $this->tableFactory($nodeEntity);

        return $this->render('initial/user_hierarchy.html.twig', [
            'structure' => base64_encode(json_encode($structure)),
            'table' => $table,
        ]);
    }

    protected function getGenealogyStructure(?User $nodeEntity, ?User $currentUser): array
    {
        $structure = [];

        if($nodeEntity) {

            if($nodeEntity != $currentUser) {
                // if(!$currentUser || !$this->affiliationService->hasChild($currentUser, $nodeEntity)) {
                //     // Check if user has permission to view the child. Else
                //     throw new AccessForbiddenException('Access to nodes outside your hierarchy is prohibited.');
                // }
            }

            $parent = $nodeEntity->getParent();

            $children = $this->affiliationService->getChildren($nodeEntity, ['maxDepth' => 4]);

            if($parent) {
                // start from an empty container; do not render node above the parent
                $structure = $this->createStructureItem($structure, $parent, null);
            }

            // render the target node after parent or as root node if no parent
            $structure = $this->createStructureItem($structure, $nodeEntity, $parent);

            foreach($children->fetchAllAssociative() as $key => $item) {
                /** @var User */
                $user = $this->entityManager->getRepository(User::class)->find($item['id']);
                $structure = $this->createStructureItem($structure, $user, $user->getParent());
            };
        }

        return $structure;
    }

    protected function tableFactory(User $nodeEntity): TableBuilder
    {
        $table = new TableBuilder('hierarchy');

        $table->setColumns([
            'id', 
            'email', 
            'parent', 
            'others'
        ]);

        // Filter columns
        
        $table
            ->setRows($this->affiliationService->getChildren($nodeEntity)->fetchAllAssociative())
        ;

        // $table->setBatchActions(true, 0);

        $table
            ->getPaginator()
            ->setItemsPerPage(2)
            ->setUrlPattern('g/b')
        ;

        // dd($table);
        return $table;

    }

    /**
     * @param array $structure  The structure container
     * @param User $user        The node to be created
     * @param null|User $parent The parent of the node
     */
    private function createStructureItem(array $structure, User $user, ?User $parent = null): array
    {
        $urlGenerator = $this->adminUrlGenerator->setRoute(self::ROUTE_NAME, [
            'entityId' => $user->getId(),
        ]);

        $structure[] = [
            'id' => $user->getId(),
            'parent' => $parent?->getId(),
            'value' => $user->getEmail(),
            'hasChildren' => $this->affiliationService->hasChildren($user),
            'hierarchyUrl' => $urlGenerator->generateUrl(),
            'editUrl' => '',
        ];

        return $structure;
    }
}
