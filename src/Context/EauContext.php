<?php

namespace App\Context;

use App\Entity\User\Notification;
use App\Entity\User\User;
use App\Service\ConfigurationService;
use App\Service\ModalService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

final class EauContext
{
    public function __construct(
        protected ConfigurationService $configurationService,
        protected ModalService $modalService,
        protected Environment $twig,
        protected EntityManagerInterface $entityManager
    ) {
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

    /**
     * Get Notifications of a user in descending order
     *
     * This is required to reduce the sorting load and iterative filtering for better optimization
     *
     * @return array
     */
    public function getNotifications(?User $user, array $options = []): array
    {
        if(!$user) {
            return [];
        }

        $optionResolver = new OptionsResolver();

        $optionResolver->setDefaults([
            'criteria' => [],
            'orderBy' => ['id' => 'DESC'],
            'limit' => null,
            'offset' => null,
        ]);

        $optionResolver
            ->setAllowedTypes('criteria', 'array')
            ->setAllowedTypes('orderBy', 'array')
            ->setAllowedTypes('limit', ['integer', 'null'])
            ->setAllowedTypes('offset', ['integer', 'null'])
        ;

        $options = $optionResolver->resolve($options);

        $notificationRepository = $this->entityManager->getRepository(Notification::class);

        $criteria = array_replace($options['criteria'], [
            'user' => $user,
        ]);

        return $notificationRepository->findBy(
            $criteria,
            $options['orderBy'],
            $options['limit'],
            $options['offset']
        );
    }
}
