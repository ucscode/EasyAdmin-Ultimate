<?php

namespace App\Twig;

use App\Configuration\ContentSlotPattern;
use App\Entity\ContentSlot;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Webmozart\Assert\Assert;

class ContentSlotExtension extends AbstractExtension
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected RequestStack $requestStack,
        protected ContentSlotPattern $contentSlotPattern
    ) {
        //
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('_slot', [$this, 'contentSlotCallback'])
        ];
    }

    /**
     * The initial method that is called when _slot() is used in twig.
     *
     * This will search in the database for content that matches the slot name
     * and render them base on certain criteria.
     *
     * @param string $slotName  The name of the slot
     * @return string           All content added to the named slot
     */
    public function contentSlotCallback(string $slotName): string
    {
        $queryBuilder = $this->entityManager->getRepository(ContentSlot::class)->createQueryBuilder('entity');

        $queryBuilder
            ->where("entity.slots LIKE :slot")
            ->andWhere('entity.enabled = :enabled')
            ->setParameter('slot', sprintf('%%"%s"%%', $slotName))
            ->setParameter('enabled', true)
            ->orderBy('entity.sort', 'ASC')
        ;

        $entities = $queryBuilder->getQuery()->getResult();

        $contentStack = array_map(fn (ContentSlot $entityInstance) => $this->getSlotContent($entityInstance), $entities);

        return implode("\n", array_filter($contentStack));
    }

    /**
     * Get the content to be rendered in a slot by matching the current controller parent classes
     *
     * This method checks if the current controller class is subclass of some targeted classes
     * and then returns the content of the ContentSlot entity if evaluated to true
     *
     * @param ContentSlot $entityInstance   The entity containing the content and targeted areas
     * @return string|null                  A string if controller has matching subclass, false otherwise
     */
    protected function getSlotContent(ContentSlot $entityInstance): ?string
    {
        // Get filtered list of all ancestor classes not equal to null

        $ancestorsContainer = array_filter(
            array_map(
                fn (ParameterBag $pattern) => $pattern->get(ContentSlotPattern::ACCESS_PARENT_FQCN), // (string) ancestor class
                $this->contentSlotPattern->getPatterns() // (array) patterns
            ),
            fn (?string $parentFQCN) => $parentFQCN !== null
        );

        // Get targeted areas where the contents are allowed to render

        foreach($entityInstance->getTargets() as $patternKey) {

            if($pattern = $this->contentSlotPattern->getPattern($patternKey)) {

                $parentFQCN = $pattern->get(ContentSlotPattern::ACCESS_PARENT_FQCN); // (string) single parent

                if($parentFQCN === null) {
                    // ensure that current controller is not part of the ancestors container
                    if(!$this->isSubClassOf($this->getControllerClassName(), $ancestorsContainer)) {
                        return $entityInstance->getContent();
                    }

                    continue;
                }

                if($this->isSubClassOf($this->getControllerClassName(), [$parentFQCN])) {
                    return $entityInstance->getContent();
                }
            }
        }

        return null;
    }

    /**
     * Check if the current controller is a subclass of any provided class names.
     *
     * @param string $controllerFqcn    The fully qualified class name (FQCN) of the current controller.
     * @param array $parentClasses      An array of parent class FQCNs to check against.
     * @return bool                     True if the current controller is a subclass of any provided class, false otherwise.
     */
    protected function isSubClassOf(string $controllerFqcn, array $parentClasses): bool
    {
        Assert::allString($parentClasses);

        foreach($parentClasses as $parentObjectFqcn) {
            if(is_subclass_of($controllerFqcn, $parentObjectFqcn) || $controllerFqcn === $parentObjectFqcn) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieves the fully qualified class name (FQCN) of the current controller.
     *
     * This method first checks if the request is within the EasyAdmin context and retrieves the dashboard controller FQCN.
     * If not, it falls back to retrieving the original controller from the request attributes.
     *
     * @return string|null The FQCN of the current controller, or null if it cannot be determined.
     */
    private function getControllerClassName(): ?string
    {
        /**
         * @var \EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext|null
         */
        $easyAdminContext = $this->requestStack->getCurrentRequest()->attributes->get(EA::CONTEXT_REQUEST_ATTRIBUTE);

        if($easyAdminContext) {
            return $easyAdminContext->getDashboardControllerFqcn();
        };

        $originalController = $this->requestStack->getCurrentRequest()->attributes->get('_controller');

        if(is_array($originalController)) {
            // get only the class name (@ignore method)
            $originalController = $originalController[0];
        }

        $controllerClass = explode('::', $originalController)[0];

        return $controllerClass ?: null;
    }
}
