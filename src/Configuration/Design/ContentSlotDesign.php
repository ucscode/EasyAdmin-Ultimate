<?php

namespace App\Configuration\Design;

class ContentSlotDesign
{
    protected ?string $name = null;
    protected ?string $title = null;
    protected ?string $markerInterface = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getMarkerInterface(): ?string
    {
        return $this->markerInterface;
    }

    public function setMarkerInterface(?string $markerInterface): static
    {
        $this->markerInterface = $markerInterface;

        return $this;
    }
}
