<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReconciliationBatch extends Model
{
    protected $fillable = [
        'batch_code',
        'batch_type',
        'business_date',
        'business_month',
        'status',
        'total_files',
        'ready_files',
        'total_records',
        'matched_records',
        'exception_records',
        'started_at',
        'completed_at',
        'error_message',
    ];

    protected $casts = [
        'business_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function stagingTransactions()
    {
        return $this->hasMany(StagingTransaction::class, 'batch_id');
    }

    public function reconciliationResults()
    {
        return $this->hasMany(ReconciliationResult::class, 'batch_id');
    }
}