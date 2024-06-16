<?php

namespace App\Traits;

use App\Constants\ModeConstants;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract class representing a bitwise mode for granting permissions.
 *
 * The PermissionTrait class serves as a foundation for implementing bitwise mode
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
 * Concrete implementations of classes extending PermissionTrait can define additional
 * permission modes or customize the behavior of permission handling based on specific
 * requirements of the system or application.
 *
 * @author Uchenna Ajah <Ucscode>
 * @link https://github.com/ucscode
 */
trait PermissionTrait
{
    // Entity safe definition
    #[ORM\Column(type: Types::SMALLINT, length: 3)]
    protected int $mode = 0;

    public function getMode(): int
    {
        return $this->mode;
    }

    public function setMode(int $mode): static
    {
        $this->mode = $this->normalizeMode($mode);

        return $this;
    }

    public function addMode(int $mode): static
    {
        $this->mode |= $this->normalizeMode($mode);
        return $this;
    }

    public function removeMode(int $mode): static
    {
        $this->mode &= ~$this->normalizeMode($mode);
        return $this;
    }

    public function hasMode(int $mode): bool
    {
        return ($this->mode & $this->normalizeMode($mode)) === $this->normalizeMode($mode);
    }

    public function isReadable(): bool
    {
        return $this->hasMode(ModeConstants::READ);
    }

    public function isWritable(): bool
    {
        return $this->hasMode(ModeConstants::WRITE);
    }

    public function isExecutable(): bool
    {
        return $this->hasMode(ModeConstants::EXECUTE);
    }

    protected function normalizeMode(int $mode): int
    {
        if($mode & ModeConstants::EXECUTE | ModeConstants::READ | ModeConstants::WRITE) {
            return $mode;
        }

        throw new \InvalidArgumentException('Invalid Permission Mode');
    }
}
