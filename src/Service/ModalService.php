<?php

namespace App\Service;

use App\Model\Modal\Modal;
use App\Utils\Stateless\Constraint;
use Symfony\Component\HttpFoundation\RequestStack;

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
        Constraint::assertIsArrayOf($modalContainer, Modal::class);

        $this->requestStack->getSession()->set(self::SESSION_KEY, $modalContainer);

        return $this;
    }
}
