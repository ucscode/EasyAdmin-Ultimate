<?php

namespace App\Twig;

use App\Configuration\ContentSlotPattern;
use App\Entity\ContentSlot;
use App\Utils\ContentSlotUtils;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContentSlotExtension extends AbstractExtension
{
    public function __construct(
        protected EntityManagerInterface $entityManager, 
        protected RequestStack $requestStack,
        protected ContentSlotPattern $contentSlotPattern
    ) 
    {
        //
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('_slot', [$this, 'contentSlotCallback'])
        ];
    }

    public function contentSlotCallback(string $slotName): string
    {
        $contentStack = [];
        
        $criteria =  [
            'slot' => $slotName,
            'enabled' => true,
        ];

        /**
         * @var array<\App\Entity\ContentSlot>
         */
        $entities = $this->entityManager->getRepository(ContentSlot::class)->findBy($criteria, ['sort' => 'ASC']);
        
        /**
         * @var \EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext|null
         */
        $easyAdminContext = $this->requestStack->getCurrentRequest()->attributes->get(EA::CONTEXT_REQUEST_ATTRIBUTE);
        
        dd($easyAdminContext->getDashboardControllerFqcn());

        foreach($entities as $entityInstance) {

            $implementations = $easyAdminContext ? class_implements($easyAdminContext->getDashboardControllerFqcn()) : [];

            $contentStack[] = $this->ContentSlotValidation($entityInstance, $implementations ?: []);
        }

        return implode("\n", array_filter($contentStack));
    }

    protected function ContentSlotValidation(ContentSlot $entityInstance, array $implementations): ?string
    {
        // array_flip([0 => 'TARGET_*']); // ['TARGET_*' => 0]
        $flipTargets = array_flip($entityInstance->getTargets());

        // Return filtered array of matching keys between TARGET_MAPPER & $flipTargets
        $targets = array_intersect_key(ContentSlotUtils::getMappers(), $flipTargets);

        // Return filtered array of matching values between $targets & $implementations
        $targetIntersection = array_intersect($targets, $implementations);

        /**
         * Found a matching Interface. E.G:
         *
         * - AdminControllerInterface
         * - UserControllerInterface
         * - SecurityControllerInterface
         */
        if(!empty($targetIntersection)) {
            return $entityInstance->getContent();
        }

        /**
         * No matching Interface? Then:
         *
         * - empty($implementations) = Not within Easy Admin Range
         *
         * - null in $targets = `TARGET_OTHERS`
         */
        if(empty($implementations) && in_array(null, $targets, true)) {
            return $entityInstance->getContent();
        }

        return null;
    }
}
