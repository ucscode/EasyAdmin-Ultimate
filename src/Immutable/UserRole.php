<?php

namespace App\Immutable;

use ReflectionClass;

final class UserRole
{
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_MANAGER = 'ROLE_MANAGER';
    public const ROLE_EDITOR = 'ROLE_EDITOR';
    public const ROLE_AUTHOR = 'ROLE_AUTHOR';
    public const ROLE_MODERATOR = 'ROLE_MODERATOR';
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_MEMEBER = 'ROLE_MEMBER';
    public const ROLE_SUBSCRIBER = 'ROLE_SUBSCRIBER';
    public const ROLE_VIEWER = 'ROLE_VIEWER';
    public const ROLE_GUEST = 'ROLE_GUEST';
    public const ROLE_CONTRIBUTOR = 'ROLE_CONTRIBUTOR';
    public const ROLE_AUDITOR = 'ROLE_AUDITOR';
    public const ROLE_ANALYIST = 'ROLE_ANALYIST';
    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';
    public const ROLE_MERCHANT = 'ROLE_MERCHANT';
    public const ROLE_MARKETER = 'ROLE_MARKETER';
    public const ROLE_DEVELOPER = 'ROLE_DEVELOPER';
    public const ROLE_STUDENT = 'ROLE_STUDENT';
    public const ROLE_TEACHER = 'ROLE_TEACHER';
    public const ROLE_SUPPORT = 'ROLE_SUPPORT';

    public static function all(bool $readable = false): array
    {
        $constants = (new ReflectionClass(self::class))->getConstants();
        if($readable) {
            $mapper = array_map(
                fn($value) => trim(str_replace(
                    ['ROLE', '_'],
                    ['', ' '],
                    $value, 
                )),
                $constants
            );
            $constants = array_combine($mapper, $constants);
        }
        return $constants;
    }
}
