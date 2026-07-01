<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StagingTransaction extends Model
{
    protected $fillable = [
        'batch_id',
        'source_id',
        'source_file_id',
        'transaction_id',
        'consumer_number',
        'account_number',
        'amount',
        'transaction_date',
        'transaction_time',
        'settlement_ref',
        'utr_number',
        'settlement_date',
        'settlement_amount',
        'transaction_status',
        'payment_status',
        'period_type',
        'business_date',
        'business_month',
        'cleaning_status',
        'validation_errors',
        'raw_payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'settlement_amount' => 'decimal:2',
        'transaction_date' => 'date',
        'settlement_date' => 'date',
        'business_date' => 'date',
        'raw_payload' => 'array',
    ];

    public function batch()
    {
        return $this->belongsTo(ReconciliationBatch::class, 'batch_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function sourceFile()
    {
        return $this->belongsTo(SourceFile::class);
    }
}