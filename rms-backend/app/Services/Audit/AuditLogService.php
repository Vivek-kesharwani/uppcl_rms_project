<?php

namespace App\Services\Audit;

use App\Models\AuditLog;

class AuditLogService
{
    public function log(
        string $action,
        string $module,
        ?string $description = null
    ): void {

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => strtoupper($action),
            'module' => strtoupper($module),
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}