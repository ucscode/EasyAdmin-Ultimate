<?php

namespace App\Entity\Slot;

use App\Repository\Slot\SlotRepository;
use App\Traits\ConstantTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlotRepository::class)]
class Slot implements SlotInterface
{
    use ConstantTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::JSON)]
    private array $positions = [];

    #[ORM\Column(type: Types::JSON)]
    private array $targets = [];

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
        $this->createdAt = new \DateTime();
        $this->sort = 0;
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

    public function getPositions(): array
    {
        return $this->positions;
    }

    public function setPositions(array $positions): static
    {
        $this->positions = $positions;

        return $this;
    }

    public function hasPosition(string $slot): bool
    {
        return in_array($slot, $this->positions, true);
    }

    public function addPosition(string $slot): static
    {
        if(!$this->hasPosition($slot)) {
            $this->positions[] = $slot;
        }

        return $this;
    }

    public function removePosition(string $slot): static
    {
        if(false !== $index = array_search($slot, $this->positions, true)) {
            unset($this->positions[$index]);
            $this->positions = array_values($this->positions);
        }

        return $this;
    }

    public function getTargets(): array
    {
        return $this->targets;
    }

    public function setTargets(array $targets): static
    {
        $this->targets = array_values(array_unique($targets));

        return $this;
    }

    public function hasTarget(string $target): bool
    {
        return in_array($target, $this->targets, true);
    }

    public function addTarget(string $target): static
    {
        if(!$this->hasTarget($target)) {
            $this->targets[] = $target;
        }

        return $this;
    }

    public function removeTarget(string $target): static
    {
        $index = array_search($target, $this->targets, true);

        if($index !== false) {
            unset($this->targets[$index]);
            $this->targets = array_values($this->targets);
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
