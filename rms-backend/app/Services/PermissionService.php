<?php

namespace App\Services;

use App\Models\User;

class PermissionService
{
    public static function allows(User $user, string $permission): bool
    {
        return match ($permission) {
            'VIEW_DASHBOARD' => IdentityService::canViewDashboard($user),

            'UPLOAD_AGENCY_FILE' => IdentityService::canUploadAgencyFile($user),
            'UPLOAD_BILLING_FILE' => IdentityService::canUploadBillingFile($user),
            'UPLOAD_BANK_FILE' => IdentityService::canUploadBankFile($user),

            'RUN_RECONCILIATION' => IdentityService::canRunReconciliation($user),

            'VIEW_EXCEPTIONS' => IdentityService::canViewExceptions($user),
            'MANAGE_EXCEPTIONS' => IdentityService::canManageExceptions($user),

            'VIEW_REPORTS' => IdentityService::canViewReports($user),

            default => false,
        };
    }
}