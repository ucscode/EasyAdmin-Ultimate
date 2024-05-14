<?php

namespace App\Service;

use App\Model\BsModal\BsModal;
use Symfony\Component\HttpFoundation\RequestStack;

class ModalService
{
    public const SESSION_KEY = 'flash.modals';

    public function __construct(protected RequestStack $requestStack)
    {

    }

    public function addModal(BsModal $modal): static
    {
        $modalContainer = $this->getModals();

        if (!in_array($modal, $modalContainer, true)) {
            $modalContainer[] = $modal;
            $this->setModals($modalContainer);
        }

        return $this;
    }

    public function removeModal(BsModal $modal): static
    {
        $modalContainer = $this->getModals();

        if(false !== ($index = array_search($modal, $modalContainer, true))) {
            unset($modalContainer[$index]);
            $this->setModals(array_values($modalContainer));
        }

        return $this;
    }

    /**
     * @return \App\Model\BsModal\BsModal[]
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
     * @param \App\Model\BsModal\BsModal[] $modalContainer
     */
    protected function setModals(array $modalContainer): static
    {
        $this->requestStack->getSession()->set(self::SESSION_KEY, $modalContainer);

        return $this;
    }
}
