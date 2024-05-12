<?php

namespace App\Model\BsModal;

class BsModal
{
    protected ?string $id = null;
    protected ?string $name = null;
    protected ?string $title = null;
    protected ?string $content = null;
    protected string|bool $backdrop = true;
    protected bool $keyboardEnabled = true;
    protected bool $closeButtonHidden = true;
    protected array $buttons = [];
    protected array $htmlClassNames = [];
    protected bool $visible = false;

    public function __construct(?string $content = null, ?string $id = null, bool $visible = false)
    {
        $this->content = $content;
        $this->id = $id;
        $this->visible = $visible;
        $this->addButton(new BsModalButton('close'));
    }

    public function setId(?string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setCloseButtonHidden(bool $closeButtonHidden): static
    {
        $this->closeButtonHidden = $closeButtonHidden;

        return $this;
    }

    public function isCloseButtonHidden(): bool
    {
        return $this->closeButtonHidden;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function addButton(?BsModalButton $button): static
    {
        if($button && !in_array($button, $this->buttons)) {
            $this->buttons[] = $button;
        }

        return $this;
    }

    public function removeButton(?BsModalButton $button): static
    {
        $index = array_search($button, $this->buttons, true);

        if($button && $index !== false) {
            unset($this->buttons[$index]);
            $this->buttons = array_values($this->buttons);
        }

        return $this;
    }

    public function getButtons(): array
    {
        return $this->buttons;
    }

    public function getButton(int $index): ?BsModalButton
    {
        return $this->buttons[$index] ?? null;
    }

    public function sortButtons(callable $callback): static
    {
        usort($this->buttons, $callback);
        
        return $this;
    }

    public function iterateButtons(callable $callback): static
    {
        foreach($this->buttons as $button) {
            call_user_func($callback, $button);
        }

        return $this;
    }

    public function setBackdrop(string|bool $backdrop): static
    {
        $this->backdrop = $backdrop;

        return $this;
    }

    public function getBackdrop(): string|bool
    {
        return $this->backdrop;
    }

    public function setKeyboardEnabled(bool $keyboardEnabled): static
    {
        $this->keyboardEnabled = $keyboardEnabled;

        return $this;
    }

    public function isKeyboardEnabled(): bool
    {
        return $this->keyboardEnabled;
    }

    public function setModalClassName(string $modalClassName): static
    {
        $this->htmlClassNames['modal'] = $modalClassName;

        return $this;
    }

    public function getModalClassName(): ?string
    {
        return $this->htmlClassNames['modal'] ?? null;
    }

    public function setDialogClassName(?string $dialogClassName): static
    {
        $this->htmlClassNames['dialog'] = $dialogClassName;

        return $this;
    }

    public function getDialogClassName(): ?string
    {
        return $this->htmlClassNames['dialog'] ?? null;
    }

    public function setVisible(bool $visible): static
    {
        $this->visible = $visible;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }
}