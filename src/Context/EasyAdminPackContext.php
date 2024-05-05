<?php

namespace App\Context;

class EasyAdminPackContext
{
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
     * 
     * @return string           The path to the specified theme layout.
     */
    public function getThemeLayout(string $dirname, string $layout = 'layout.html.twig'): string
    {
        return sprintf('themes/%s/%s', $dirname, $layout);
    }
}