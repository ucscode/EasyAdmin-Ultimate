<?php

namespace App\Bundle\Abstract;

use App\Enum\ModeEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

abstract class AbstractMetaEntity extends AbstractBitwiseMode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    protected ?string $metaKey = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $metaValue = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected ?\DateTimeInterface $metaTimestamp = null;

    #[ORM\Column(type: Types::SMALLINT, length: 3)]
    protected int $bitwiseMode = 0;

    public function __construct(?string $key = null, mixed $value = null, int|ModeEnum $mode = ModeEnum::READ)
    {
        if(!is_null($key)) {
            $this->setMetaKey($key);
            $this->setMetaValue($value);
        }
        $this->setMetaTimestamp(new \DateTime());
        $this->addBitwiseMode($mode);
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

    public function getMetaValueAsString(): string
    {
        $value = $this->getMetaValue();

        if(!is_scalar($value)) {
            $value = sprintf("[%s]", gettype($value));
        }

        if(is_bool($value)) {
            $value = $value ? 'On' : 'Off';
        }

        return $value;
    }

    public function setMetaTimestamp(?\DateTimeInterface $timestamp): static
    {
        $this->metaTimestamp = $timestamp;

        return $this;
    }

    public function getMetaTimestamp(): ?\DateTimeInterface
    {
        return $this->metaTimestamp;
    }
}
