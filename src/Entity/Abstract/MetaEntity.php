<?php

namespace App\Entity\Abstract;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

abstract class MetaEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    protected ?string $_key = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $_value = null;

    public function __construct(?string $key = null, mixed $value = null)
    {
        is_null($key) ?: ($this->setKey($key) && $this->setValue($value));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): ?string
    {
        return $this->_key;
    }

    public function setKey(string $key): static
    {
        $this->_key = $key;

        return $this;
    }
    
    public function getValue(): mixed
    {
        return json_decode($this->_value, true, 512, JSON_UNESCAPED_UNICODE);
    }

    public function setValue(mixed $value): static
    {
        $this->_value = json_encode($value, JSON_UNESCAPED_UNICODE);

        return $this;
    }
}
