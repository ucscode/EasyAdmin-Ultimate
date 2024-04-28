<?php

namespace App\Entity;

use App\Repository\CodeInfusionRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CodeInfusionRepository::class)]
class CodeInfusion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 25)]
    private ?string $slot = null;

    #[ORM\Column(type: Types::JSON)]
    private array $locations = [];

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\Column]
    private ?int $sort = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->setCreatedAt(new DateTime());
        $this->setSort(0);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlot(): ?string
    {
        return $this->slot;
    }

    public function setSlot(string $slot): static
    {
        $this->slot = $slot;

        return $this;
    }

    public function getLocations(): array
    {
        return $this->locations;
    }

    public function setLocations(array $locations): static
    {
        $this->locations = $locations;

        return $this;
    }

    public function hasLocation(string $location): bool
    {
        return in_array($location, $this->locations, true);
    }

    public function addLocation(string $location): static
    {
        if(!$this->hasLocation($location)) {
            $this->locations[] = $location;
        }

        return $this;
    }

    public function removeLocation(string $location): static
    {
        $index = array_search($location, $this->locations, true);

        if($index !== false) {
            unset($this->locations[$index]);
        }

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
