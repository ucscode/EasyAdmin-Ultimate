<?php

namespace App\Context;

use App\Service\ConfigurationService;
use App\Service\ModalService;
use Twig\Environment;

final class EauContext
{
    public function __construct(
        protected ConfigurationService $configurationService, 
        protected ModalService $modalService,
        protected Environment $twig
    )
    {
    }

    /**
     * Determines template resolution behavior for templates within the "@EasyAdmin" namespace.
     *
     * If a template is requested with "@EasyAdmin" namespace, Symfony searches for a local copy
     * within the `templates/bundles/EasyAdminBundle/` directory. If not found, it defaults
     * to the original template provided by the EasyAdmin Bundle.
     *
     * Using "@!EasyAdmin" (with exclamation) directs Symfony to resolve to the original
     * template within the EasyAdmin bundle, bypassing any local overrides. This prevents
     * recursion issues and allows seamless extension of the original template.
     */
    public function getTemplatePath(string $name, bool $original = false): string
    {
        return sprintf('%s/%s.html.twig', $original ? '@!EasyAdmin' : '@EasyAdmin', $name);
    }

    /**
     * Get a configuration value defined in /config/eau.yaml
     *
     * @param string $name  The configuration key (chained with dot)
     * @return mixed        The configuration value
     */
    public function getConfig(string $name): mixed
    {
        return $this->configurationService->get($name);
    }

    /**
     * @return \App\Model\Modal\Modal[]
     */
    public function getModals(bool $clearAfterAccess = false): array
    {
        $modalContainer = $this->modalService->getModals();

        if($clearAfterAccess) {
            $this->modalService->clearModals();
        }

        return $modalContainer;
    }
}
