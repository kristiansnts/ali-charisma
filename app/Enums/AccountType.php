<?php

namespace App\Enums;

enum AccountType: string
{
    case Customer = 'customer';
    case Admin = 'admin';
    case SuperAdmin = 'superadmin';

    public static function fromUserRoles(bool $isSuperAdmin, bool $isAdmin): self
    {
        if ($isSuperAdmin) {
            return self::SuperAdmin;
        }

        if ($isAdmin) {
            return self::Admin;
        }

        return self::Customer;
    }
}
