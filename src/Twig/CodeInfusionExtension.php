<?php

namespace App\Twig;

use App\Controller\Admin\Interface\AdminControllerInterface;
use App\Controller\Auth\Interface\AuthControllerInterface;
use App\Controller\User\Interface\UserControllerInterface;
use App\Entity\CodeInfusion;
use App\Utils\CodeInfusionUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CodeInfusionExtension extends AbstractExtension
{
    public const TARGET_MAPPER = [
        CodeInfusionUtils::TARGET_ADMIN => AdminControllerInterface::class,
        CodeInfusionUtils::TARGET_USER => UserControllerInterface::class,
        CodeInfusionUtils::TARGET_AUTHENTICATION => AuthControllerInterface::class,
        CodeInfusionUtils::TARGET_OTHERS => null,
    ];

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected RequestStack $requestStack
    ) {

    }

    public function getFunctions()
    {
        return [
            new TwigFunction('codeInfusion', [$this, 'codeInfusionCallback'])
        ];
    }

    public function codeInfusionCallback(string $slot, mixed $context = null): string
    {
        $contentStack = [];

        /**
         * @var array<\App\Entity\CodeInfusion>
         */
        $entities = $this->entityManager->getRepository(CodeInfusion::class)->findBy(
            [
                'slot' => $slot,
                'enabled' => true,
            ],
            ['sort' => 'ASC'], // orderBy
        );

        /**
         * @var \EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext|null
         */
        $easyAdminContext = $this->requestStack->getCurrentRequest()->attributes->get('easyadmin_context');

        foreach($entities as $entityInstance) {

            $implementations = $easyAdminContext ? class_implements($easyAdminContext->getDashboardControllerFqcn()) : [];

            $contentStack[] = $this->codeInfusionValidation($entityInstance, $implementations ?: []);
        }

        return implode("\n", array_filter($contentStack));
    }

    public function codeInfusionValidation(CodeInfusion $entityInstance, array $implementations): ?string
    {
        // array_flip([0 => 'TARGET_*']); // ['TARGET_*' => 0]
        $flipTargets = array_flip($entityInstance->getTargets());

        // Return filtered array of matching keys between TARGET_MAPPER & $flipTargets
        $targets = array_intersect_key(self::TARGET_MAPPER, $flipTargets);

        // Return filtered array of matching values between $targets & $implementations
        $targetIntersection = array_intersect($targets, $implementations);

        /**
         * Found a matching Interface. E.G:
         *
         * - AdminControllerInterface
         * - UserControllerInterface
         * - AuthControllerInterface
         */
        if(!empty($targetIntersection)) {
            return $entityInstance->getContent();
        }

        /**
         * No matching Interface? Then:
         *
         * - empty($implementations): Not within Easy Admin Range
         *
         * - null in $targets: `TARGET_OTHERS` is enabled
         */
        if(empty($implementations) && in_array(null, $targets, true)) {
            return $entityInstance->getContent();
        }

        return null;
    }
}
