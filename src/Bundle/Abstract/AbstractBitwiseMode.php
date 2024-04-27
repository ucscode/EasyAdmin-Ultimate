<?php

namespace App\Bundle\Abstract;

use App\Enum\ModeEnum;

abstract class AbstractBitwiseMode
{
    protected int $bitwiseMode = 0;

    public function getBitwiseMode(): int
    {
        return $this->bitwiseMode;
    }

    public function setBitwiseMode(int|ModeEnum $mode): static
    {
        $this->bitwiseMode = $this->getModeInteger($mode);

        return $this;
    }

    public function addBitwiseMode(int|ModeEnum $mode): static
    {
        $this->bitwiseMode |= $this->getModeInteger($mode);
        return $this;
    }

    public function removeBitwiseMode(int|ModeEnum $mode): static
    {
        $this->bitwiseMode &= ~$this->getModeInteger($mode);
        return $this;
    }

    public function hasBitwiseMode(int|ModeEnum $mode): bool
    {
        return ($this->bitwiseMode & $this->getModeInteger($mode)) === $this->getModeInteger($mode);
    }

    public function isReadable(): bool
    {
        return $this->hasBitwiseMode(ModeEnum::READ->value);
    }

    public function isWritable(): bool
    {
        return $this->hasBitwiseMode(ModeEnum::WRITE->value);
    }

    public function isExecutable(): bool
    {
        return $this->hasBitwiseMode(ModeEnum::EXECUTE->value);
    }

    private function getModeInteger(int|ModeEnum $mode): int
    {
        $modeInteger = $mode instanceof ModeEnum ? $mode->value : $mode;

        if($modeInteger & ModeEnum::EXECUTE->value | ModeEnum::READ->value | ModeEnum::WRITE->value) {
            return $modeInteger;
        }

        throw new \InvalidArgumentException('Invalid Bitwise Mode');
    }
}
