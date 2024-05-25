<?php

namespace App\Service;

use App\Model\Modal\Modal;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

/**
 * This service controls modal state between sessions and provide context that are rendered by twig
 * If you need to render a modal in your page, use this service
 *
 * @see https://github.com/webmozarts/assert
 */
class ModalService
{
    public const SESSION_KEY = 'flash.modals';

    public function __construct(protected RequestStack $requestStack)
    {
    }

    public function addModal(Modal $modal): static
    {
        $modalContainer = $this->getModals();

        if (!in_array($modal, $modalContainer, true)) {
            $modalContainer[] = $modal;
            $this->setModals($modalContainer);
        }

        return $this;
    }

    public function removeModal(Modal $modal): static
    {
        $modalContainer = $this->getModals();

        if(false !== ($index = array_search($modal, $modalContainer, true))) {
            unset($modalContainer[$index]);
            $this->setModals(array_values($modalContainer));
        }

        return $this;
    }

    /**
     * @return Modal[]
     */
    public function getModals(): array
    {
        return $this->requestStack->getSession()->get(self::SESSION_KEY) ?? [];
    }

    public function clearModals(): static
    {
        return $this->setModals([]);
    }

    /**
     * @param Modal[] $modalContainer
     */
    protected function setModals(array $modalContainer): static
    {
        Assert::allIsInstanceOf($modalContainer, Modal::class);

        $this->requestStack->getSession()->set(self::SESSION_KEY, $modalContainer);

        return $this;
    }
}
