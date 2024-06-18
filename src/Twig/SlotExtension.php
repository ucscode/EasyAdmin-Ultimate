<?php

namespace App\Twig;

use App\Configuration\Design\ContentSlotDesign;
use App\Configuration\Factory\ContentSlotDesignFactory;
use App\Entity\Slot\Slot;
use App\Service\RequestManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Webmozart\Assert\Assert;

class SlotExtension extends AbstractExtension
{
    public function __construct(
        protected EntityManagerInterface   $entityManager,
        protected RequestStack             $requestStack,
        protected ContentSlotDesignFactory $contentSlotVOFactory,
        protected RequestManager           $requestManager
    ) {
        //
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('_slot', [$this, 'slotCallback'])
        ];
    }

    /**
     * The initial method that is called when _slot() is used in twig.
     *
     * This will search in the database for content that matches the slot name
     * and render them base on certain criteria.
     *
     * @param string $position  The name of the slot
     * @return string           All content added to the named slot
     */
    public function slotCallback(string $position): string
    {
        $queryBuilder = $this->entityManager->getRepository(Slot::class)->createQueryBuilder('entity');

        $queryBuilder
            ->where("entity.positions LIKE :position")
            ->andWhere('entity.enabled = :enabled')
            ->setParameter('position', sprintf('%%"%s"%%', $position))
            ->setParameter('enabled', true)
            ->orderBy('entity.sort', 'ASC')
        ;

        $entities = $queryBuilder->getQuery()->getResult();

        $contentStack = array_map(fn (Slot $entityInstance) => $this->getSlotContent($entityInstance), $entities);

        return implode("\n", array_filter($contentStack));
    }

    /**
     * Get the content to be rendered in a slot by matching the current controller parent classes
     *
     * This method checks if the current controller class is subclass of some targeted classes
     * and then returns the content of the Slot entity if evaluated to true
     *
     * @param Slot $entityInstance   The entity containing the content and targeted areas
     * @return string|null                  A string if controller has matching subclass, false otherwise
     */
    protected function getSlotContent(Slot $entityInstance): ?string
    {
        // Get filtered list of all ancestor classes not equal to null

        $ancestorsContainer = array_filter(
            array_map(
                fn (ContentSlotDesign $slotDesign) => $slotDesign->getMarkerInterface(),
                $this->contentSlotVOFactory->getItems()
            ),
            fn (?string $parentFQCN) => $parentFQCN !== null
        );

        // Get targeted areas where the contents are allowed to render

        foreach($entityInstance->getTargets() as $patternKey) {

            if($slotDesign = $this->contentSlotVOFactory->getItem($patternKey)) {

                $parentFQCN = $slotDesign->getMarkerInterface();

                if($parentFQCN === null) {
                    // ensure that current controller is not part of the ancestors container
                    if(!$this->isSubClassOf($this->requestManager->getCurrentControllerFqcn(), $ancestorsContainer)) {
                        return $entityInstance->getContent();
                    }

                    continue;
                }

                if($this->isSubClassOf($this->requestManager->getCurrentControllerFqcn(), [$parentFQCN])) {
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
}
