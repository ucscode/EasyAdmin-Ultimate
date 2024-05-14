<?php

namespace App\Context;

use App\Service\ConfigurationService;
use App\Service\ModalService;

final class EauContext
{
    public function __construct(protected ConfigurationService $configurationService, protected ModalService $modalService)
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
    public function getBaseTemplate(string $name, bool $original = false): string
    {
        return sprintf('%s/%s.html.twig', $original ? '@!EasyAdmin' : '@EasyAdmin', $name);
    }

    /**
     * Generates the path to a theme layout within the "@EasyAdmin" bundle.
     *
     * When extending templates from the "@EasyAdmin" bundle directly, modifications are typically
     * limited to altering the layout or appearance. By creating multiple theme layouts,
     * you can interchange between different layouts for various dashboard panels.
     *
     * @param string $dirname   The directory name of the theme layout.
     * @param string $layout    The filename of the layout template (default is 'layout.html.twig').
     * @return string           The path to the specified theme layout.
     */
    public function getThemeLayout(string $dirname, string $layout = 'layout.html.twig'): string
    {
        return sprintf('themes/%s/%s', $dirname, $layout);
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
     * @return \App\Model\BsModal\BsModal[]
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
