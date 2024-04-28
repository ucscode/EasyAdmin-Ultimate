<?php

namespace App\Twig;

use App\Entity\CodeInfusion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CodeInfusionExtension extends AbstractExtension
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected RequestStack $requestStack
    )
    {
        
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('codeInfusion', [$this, 'codeInfusionCallback'])
        ];
    }

    public function codeInfusionCallback(string $slot, mixed $context = null): void
    {
        /**
         * @var array<\App\Entity\CodeInfusion>
         */
        $entities = $this->entityManager->getRepository(CodeInfusion::class)->findBy(['slot' => $slot]);

        /**
         * @var \EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext|null
         */
        $easyAdminContext = $this->requestStack->getCurrentRequest()->attributes->get('easyadmin_context');

        dd(get_class_methods($easyAdminContext), $easyAdminContext->getDashboardControllerFqcn());

        foreach($entities as $entityInstance) {
            dd($entityInstance);
        }
    }
}