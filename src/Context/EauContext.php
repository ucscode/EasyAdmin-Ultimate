<?php

namespace App\Context;

use App\Service\ConfigurationService;
use App\Utils\Stateful\BsModal\BsModal;
use Symfony\Component\HttpFoundation\RequestStack;

final class EauContext
{
    private const FLASH_MODALS_OFFSET = 'flash.modals';
    private array $modals = [];

    public function __construct(protected RequestStack $requestStack, protected ConfigurationService $configurationService)
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

    public function addModal(BsModal $modal, bool $flash = false): static
    {
        if(!in_array($modal, $this->modals)) {
            $this->modals[] = $modal;
        }

        $session = $this->requestStack->getSession();
        /**
         * @var \App\Utils\Stateful\BsModal\BsModal[]
         */
        $sessionModals = $session->get(self::FLASH_MODALS_OFFSET) ?? [];

        if ($flash && !in_array($modal, $sessionModals)) {
            $sessionModals[] = $modal;
            $session->set(self::FLASH_MODALS_OFFSET, $sessionModals);
            return $this;
        }
        
        return $this->discardModalFromSession($modal);
    }

    public function removeModal(BsModal $modal): static
    {
        if(false !== ($index = array_search($modal, $this->modals))) {
            unset($this->modals[$index]);
            $this->modals = array_values($this->modals);
            $this->discardModalFromSession($modal);
        }

        return $this;
    }

    public function getModals(): array
    {
        $session = $this->requestStack->getSession();
        /**
         * @var array
         */
        $sessionModals = $session->get(self::FLASH_MODALS_OFFSET) ?? [];
        $entireModals = array_merge($this->modals, $sessionModals);

        foreach($entireModals as $modal) {
            $this->discardModalFromSession($modal);
        }

        return $entireModals;
    }

    private function discardModalFromSession(BsModal $modal): static
    {
        $session = $this->requestStack->getSession();
        /**
         * @var \App\Utils\Stateful\BsModal\BsModal[]
         */
        $sessionModals = $session->get(self::FLASH_MODALS_OFFSET) ?? [];

        $index = array_search($modal, $sessionModals);

        if($index !== false) {
            unset($sessionModals[$index]);
        }

        $this->requestStack->getSession()->set(self::FLASH_MODALS_OFFSET, array_values(array_unique($sessionModals)));

        return $this;
    }
}
