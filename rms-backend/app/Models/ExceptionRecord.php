<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExceptionRecord extends Model
{
    protected $fillable = [
        'reconciliation_master_id',
        'reconciliation_result_id',
        'case_number',
        'txn_id',
        'exception_code',
        'severity',
        'priority',
        'variance_amount',
        'status',
        'assigned_role',
        'assigned_to',
        'assigned_at',
        'opened_at',
        'remarks',
        'resolution_notes',
        'root_cause',
        'resolved_by',
        'resolved_at',
        'verified_at',
        'verified_by',
        'closed_at',
        'closed_by',
        'sla_due_at',
        'sla_breached',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'opened_at' => 'datetime',
        'resolved_at' => 'datetime',
        'verified_at' => 'datetime',
        'closed_at' => 'datetime',
        'sla_due_at' => 'datetime',
        'sla_breached' => 'boolean',
        'variance_amount' => 'decimal:2',
    ];

    public function reconciliationResult()
    {
        return $this->belongsTo(ReconciliationResult::class);
    }
}