<?php

namespace App\Services;

use App\Models\User;

class IdentityService
{
    public static function domain(User $user): ?string
    {
        return $user->domain?->name;
    }

    public static function systemRole(User $user): ?string
    {
        return $user->systemRole?->name;
    }

    public static function isHQ(User $user): bool
    {
        return self::domain($user) === 'HQ';
    }

    public static function isDiscom(User $user): bool
    {
        return self::domain($user) === 'DISCOM';
    }

    public static function isAgency(User $user): bool
    {
        return self::domain($user) === 'AGENCY';
    }

    public static function isAdmin(User $user): bool
    {
        return self::systemRole($user) === 'ADMIN';
    }

    public static function canUploadAgencyFile(User $user): bool
    {
        return self::isHQ($user) || self::isAgency($user);
    }

    public static function canUploadBillingFile(User $user): bool
    {
        return self::isHQ($user);
    }

    public static function canUploadBankFile(User $user): bool
    {
        return self::isHQ($user);
    }

    public static function canRunReconciliation(User $user): bool
    {
        return self::isHQ($user);
    }

    public static function canViewDashboard(User $user): bool
    {
        return self::isHQ($user) || self::isDiscom($user) || self::isAgency($user);
    }

    public static function canViewExceptions(User $user): bool
    {
        return self::isHQ($user) || self::isDiscom($user);
    }

    public static function canManageExceptions(User $user): bool
    {
        return self::isHQ($user);
    }

    public static function canViewReports(User $user): bool
    {
        return self::isHQ($user) || self::isDiscom($user) || self::isAgency($user);
    }
}