<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReconciliationBatch extends Model
{
    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | Batch Information
        |--------------------------------------------------------------------------
        */

        'batch_code',
        'batch_type',

        'business_date',
        'business_month',

        /*
        |--------------------------------------------------------------------------
        | Execution
        |--------------------------------------------------------------------------
        */

        'status',

        'run_mode',

        'triggered_by',

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        'total_files',
        'ready_files',

        'total_records',
        'matched_records',
        'exception_records',

        /*
        |--------------------------------------------------------------------------
        | Timing
        |--------------------------------------------------------------------------
        */

        'started_at',
        'completed_at',

        /*
        |--------------------------------------------------------------------------
        | Errors
        |--------------------------------------------------------------------------
        */

        'error_message',
    ];

    protected $casts = [

        'business_date' => 'date',

        'started_at' => 'datetime',

        'completed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Files selected for this batch.
     */
    public function batchFiles()
    {
        return $this->hasMany(BatchFile::class, 'batch_id');
    }

    /**
     * Staging records generated from selected files.
     */
    public function stagingTransactions()
    {
        return $this->hasMany(StagingTransaction::class, 'batch_id');
    }

    /**
     * Final reconciliation results.
     */
    public function reconciliationResults()
    {
        return $this->hasMany(ReconciliationResult::class, 'batch_id');
    }

    /**
     * User who started the batch.
     */
    public function triggeredBy()
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }
}