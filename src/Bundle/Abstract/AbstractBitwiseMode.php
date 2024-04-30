<?php

namespace App\Bundle\Abstract;

use App\Enum\ModeEnum;

/**
 * Abstract class representing a bitwise mode for granting permissions.
 *
 * The AbstractBitwiseMode class serves as a foundation for implementing bitwise mode
 * representations used to grant permissions such as read, write, and execute on various
 * resources or entities within a system. The bitwise mode is represented as an integer
 * value where each bit corresponds to a specific permission.
 *
 * The typical representation of permissions using bitwise mode numbers is as follows:
 *
 * - Read Permission: 4 (binary 100)
 * - Write Permission: 2 (binary 010)
 * - Execute Permission: 1 (binary 001)
 *
 * By combining these mode numbers using bitwise operations (OR, AND, XOR), different
 * combinations of permissions can be represented and applied to resources. For example:
 *
 * - Read and Write Permissions: 6 (binary 110)
 * - Read, Write, and Execute Permissions: 7 (binary 111)
 *
 * Concrete implementations of classes extending AbstractBitwiseMode can define additional
 * permission modes or customize the behavior of permission handling based on specific
 * requirements of the system or application.
 *
 * @author Uchenna Ajah <Ucscode>
 *
 * @link https://github.com/ucscode
 */
abstract class AbstractBitwiseMode
{
    protected int $bitwiseMode = 0;

    public function getBitwiseMode(): int
    {
        return $this->bitwiseMode;
    }

    public function setBitwiseMode(int|ModeEnum $mode): static
    {
        $this->bitwiseMode = $this->normalizeMode($mode);

        return $this;
    }

    public function addBitwiseMode(int|ModeEnum $mode): static
    {
        $this->bitwiseMode |= $this->normalizeMode($mode);
        return $this;
    }

    public function removeBitwiseMode(int|ModeEnum $mode): static
    {
        $this->bitwiseMode &= ~$this->normalizeMode($mode);
        return $this;
    }

    public function hasBitwiseMode(int|ModeEnum $mode): bool
    {
        return ($this->bitwiseMode & $this->normalizeMode($mode)) === $this->normalizeMode($mode);
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

    private function normalizeMode(int|ModeEnum $mode): int
    {
        $modeInteger = $mode instanceof ModeEnum ? $mode->value : $mode;

        if($modeInteger & ModeEnum::EXECUTE->value | ModeEnum::READ->value | ModeEnum::WRITE->value) {
            return $modeInteger;
        }

        throw new \InvalidArgumentException('Invalid Bitwise Mode');
    }
}
