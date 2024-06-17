<?php

namespace App\Configuration\Design;

use App\Constants\ModeConstant;
use Closure;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Webmozart\Assert\Assert;

class UserPropertyFieldDesign
{
    protected ?string $name = null;
    protected ?string $label = null;
    protected mixed $value = null;
    protected string $fieldFqcn = TextField::class;
    protected ?FieldInterface $fieldInstance = null;
    protected int $mode = ModeConstant::READ | ModeConstant::WRITE;
    protected ?string $description = null;
    private ?Closure $config = null;

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getFieldFqcn(): string
    {
        return $this->fieldFqcn;
    }

    public function setFieldFqcn(string $fieldFqcn): static
    {
        Assert::implementsInterface($fieldFqcn, FieldInterface::class);

        $this->fieldFqcn = $fieldFqcn;

        return $this;
    }

    public function getFieldInstance(): ?FieldInterface
    {
        return $this->fieldInstance;
    }

    public function setFieldInstance(FieldInterface $field): static
    {
        $this->fieldInstance = $field;

        return $this;
    }

    public function getMode(): int
    {
        return $this->mode;
    }

    public function setMode(int $mode): static
    {
        $this->mode = $mode;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getConfig(): ?Closure
    {
        return $this->config;
    }

    public function setConfig(callable $config): static
    {
        $this->config = Closure::fromCallable($config);

        return $this;
    }
}
