<?php

namespace App\Entity\Abstracts;

use App\Component\Abstracts\AbstractPermission;
use App\Enum\ModeEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract class representing a meta entity with key-value pairs, timestamp, and access mode.
 *
 * The AbstractEntityMeta class serves as a foundation for managing meta information associated
 * with entities in a system. Meta information typically includes attributes such as key,
 * value, timestamp, and access mode (permissions).
 *
 * Meta information is commonly used to store additional data or properties related to an
 * entity that are not part of its core structure but provide supplementary context or
 * configuration. Examples of meta information include settings, configurations, or
 * annotations specific to an entity.
 *
 * Attributes:
 * - id: The unique identifier of the meta entity.
 * - metaKey: The key or name of the meta attribute.
 * - metaValue: The value associated with the meta attribute.
 * - metaTimestamp: The timestamp indicating when the meta information was created.
 * - mode: The bitwise mode representing access permissions for the meta entity.
 *
 * @see \App\Component\Abstracts\AbstractPermission
 *
 * Concrete implementations of classes extending AbstractEntityMeta can define additional
 * attributes or customize the behavior of meta management based on specific requirements
 * of the system or application.
 *
 * @author Uchenna Ajah
 * @link https://github.com/ucscode
 */
abstract class AbstractEntityMeta extends AbstractPermission
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
    protected int $mode = 0;

    public function __construct(?string $key = null, mixed $value = null, int|ModeEnum $mode = ModeEnum::READ)
    {
        if(!is_null($key)) {
            $this->setMetaKey($key);
            $this->setMetaValue($value);
        }

        $this->setMetaTimestamp(new \DateTime());
        $this->addMode($mode);
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

        if(!is_scalar($value) && !is_null($value)) {
            $value = sprintf("[%s]", gettype($value));
        }

        if(is_bool($value)) {
            $value = $value ? 'On' : 'Off';
        }

        return $value ?? '';
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
