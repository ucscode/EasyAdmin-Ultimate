<?php

namespace App\Entity\Abstract;

use App\Enum\ModeEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

abstract class AbstractMetaEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    protected ?string $metaKey = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $metaValue = null;

    #[ORM\Column(type: Types::SMALLINT)]
    protected ?int $metaMode = null;

    public function __construct(?string $key = null, mixed $value = null)
    {
        is_null($key) ?: ($this->setMetaKey($key) && $this->setMetaValue($value));
        $this->setMetaMode(ModeEnum::READ_WRITE);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMetaKey(): ?string
    {
        return $this->metaKey;
    }

    public function setMetaKey(string $key): static
    {
        $this->metaKey = $key;

        return $this;
    }
    
    public function getMetaValue(): mixed
    {
        return json_decode($this->metaValue, true, 512, JSON_UNESCAPED_UNICODE);
    }

    public function setMetaValue(mixed $value): static
    {
        $this->metaValue = json_encode($value, JSON_UNESCAPED_UNICODE);

        return $this;
    }

    public function getMetaMode(): ?int
    {
        return $this->metaMode;
    }

    public function setMetaMode(?int $mode): static
    {
        $this->metaMode = $mode;

        return $this;
    }
}
