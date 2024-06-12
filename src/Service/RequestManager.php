<?php

namespace App\Service;

use App\Controller\Admin\Interfaces\AdminControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestManager
{
    public function __construct(protected RequestStack $requestStack)
    {
        
    }

    public function getRequestStack(): RequestStack
    {
        return $this->requestStack;
    }

    /**
     * Retrieves the fully qualified class name (FQCN) of the current controller.
     *
     * This method first checks if the request is within the EasyAdmin context and retrieves the dashboard controller FQCN.
     * If not, it falls back to retrieving the original controller from the request attributes.
     *
     * @return string|null The FQCN of the current controller, or null if it cannot be determined.
     */
    public function getCurrentControllerFqcn(): ?string
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

    /**
     * Checks if the current controller implements a specified interface or interfaces.
     *
     * This method accepts a single interface or an array of interfaces and determines if
     * the current controller, identified by its fully qualified class name (FQCN), implements
     * any of the specified interfaces.
     *
     * @param string|array $interface A single interface name or an array of interface names to check against.
     * @return bool True if the current controller implements any of the specified interfaces, false otherwise.
     */
    public function currentControllerImplementsInteface(string|array $interface): bool
    {
        return !empty(array_intersect(
            is_string($interface) ? [$interface] : $interface,
            class_implements($this->getCurrentControllerFqcn())
        ));
    }

    /**
     * Checks if the current controller is part of the admin control panel.
     *
     * This method determines whether the current controller implements the
     * AdminControllerInterface, indicating it is part of the administrative control panel.
     *
     * @return bool True if the current controller implements AdminControllerInterface, false otherwise.
     */
    public function isAdminControllerRequest(): bool
    {
        return $this->currentControllerImplementsInteface(AdminControllerInterface::class);
    }
}