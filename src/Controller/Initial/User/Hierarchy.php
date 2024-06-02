<?php

namespace App\Controller\Initial\User;

use App\Controller\Initial\Abstracts\AbstractInitialDashboardController;
use App\Entity\User\User;
use App\Exceptions\AccessForbiddenException;
use App\Service\AffiliationService;
use App\Utility\Table\Cell;
use App\Utility\Table\TableBuilder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Ucscode\Paginator\Paginator;

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

        $userEntity = $this->getUser();

        if($this->parameters->get('entityId')) {
            $userEntity = $this->entityManager->getRepository(User::class)->find($this->parameters->get('entityId'));
        }

        $structure = $this->getGenealogyStructure($userEntity);
        $tableBuilder = $this->createTableBuilder($request, $userEntity);

        return $this->render('initial/user_hierarchy.html.twig', [
            'structure' => base64_encode(json_encode($structure)),
            'table' => $tableBuilder,
        ]);
    }

    /**
     * Recursively create a genealogy structure for each user.
     * The structure is a simple associate data showing each user and their parent id
     * 
     * @param User $userEntity  The entity that will be used as root entity when generating the structure
     * @return array   The generate hierarchical structure
     */
    protected function getGenealogyStructure(?User $userEntity): array
    {
        $currentUser = $this->getUser();

        $structure = [];

        if($userEntity) {

            if($userEntity != $currentUser) {
                // if(!$currentUser || !$this->affiliationService->hasChild($currentUser, $userEntity)) {
                //     // Check if user has permission to view the child. Else
                //     throw new AccessForbiddenException('Access to nodes outside your hierarchy is prohibited.');
                // }
            }

            $parent = $userEntity->getParent();

            $children = $this->affiliationService->getChildren($userEntity, ['maxDepth' => 4]);

            if($parent) {
                // start from an empty container; do not render node above the parent
                $structure = $this->createStructureItem($structure, $parent, null);
            }

            // render the target node after parent or as root node if no parent
            $structure = $this->createStructureItem($structure, $userEntity, $parent);

            foreach($children->fetchAllAssociative() as $key => $item) {
                /** @var User */
                $user = $this->entityManager->getRepository(User::class)->find($item['id']);
                $structure = $this->createStructureItem($structure, $user, $user->getParent());
            };
        }

        return $structure;
    }

    /**
     * Create a table showing a list of the current user's referrals
     * 
     * @param Request $request  The Request object to fetch the current page for pagination
     * @param User $userEntity  The user entity that the table will be generated for
     * @return TableBuilder
     */
    protected function createTableBuilder(Request $request, User $userEntity): TableBuilder
    {
        // Pagination Pattern;
        $urlPattern = $this->generateGenealogyUrl($userEntity) . sprintf('&page=%s', Paginator::NUM_PLACEHOLDER);

        // TableBuilder Instance
        $table = new TableBuilder('hierarchy');

        // Table Header/Columns
        $table->setColumns(['id', 'email', 'parent', 'level']);

        // Table Data/Associatives From Database
        $table->setRows($this->affiliationService->getChildren($userEntity)->fetchAllAssociative());

        // Enable Checkboxes
        // $table->setBatchActions(true, 0);

        $table
            ->getPaginator()
            ->setItemsPerPage(15)
            ->setUrlPattern($urlPattern)
            ->setCurrentPage($request->query->get('page') ?: 1)
        ;  

        $table->setConfigurator("parent-transformer", function(Cell $cell) {
            if(in_array($cell->getMeta('label'), ['id', 'parent'], true)) {
                $cell->setHidden(true);
                /** @var ?User */
                $parent = $this->entityManager->getRepository(User::class)->find($cell->getMeta('value'));
                !$parent ?: $cell->setValue($parent->getEmail());
            }
        });

        return $table;
    }

    /**
     * Generate URL pointing to a user's genealogy tree
     * 
     * @param User $user The user url to generate
     * @param array $parameter  Addition query parameter to add to the url
     * @return string   The generated URL
     */
    protected function generateGenealogyUrl(User $user, array $parameters = []): string
    {
        return $this->adminUrlGenerator->setRoute(
            self::ROUTE_NAME, 
            array_replace($parameters, [
                'entityId' => $user->getId(),
            ])
        )->generateUrl();
    }

    /**
     * The structure generated is converted to javascript object and used by TreeDataNext.js to render interactive genealogy tree
     * 
     * @param array $structure  The structure container
     * @param User $user        The node to be created
     * @param null|User $parent The parent of the node
     * 
     * @see https://github.com/ucscode/treeDataNext.js
     */
    private function createStructureItem(array $structure, User $user, ?User $parent = null): array
    {
        $structure[] = [
            'id' => $user->getId(),
            'parent' => $parent?->getId(),
            'value' => $user->getEmail(),
            'hasChildren' => $this->affiliationService->hasChildren($user),
            'hierarchyUrl' => $this->generateGenealogyUrl($user),
            'editUrl' => '',
        ];

        return $structure;
    }
}
