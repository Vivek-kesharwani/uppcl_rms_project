<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReconciliationResult extends Model
{
    protected $fillable = [
        'batch_id',
        'matching_set_id',
        'left_source_id',
        'right_source_id',
        'left_record_id',
        'right_record_id',
        'transaction_id',
        'consumer_number',
        'settlement_ref',
        'utr_number',
        'period_type',
        'business_date',
        'business_month',
        'result_status',
        'exception_code',
        'variance_amount',
        'visible_to_source_id',
        'rule_results',
        'remarks',
    ];

    protected $casts = [
        'business_date' => 'date',
        'variance_amount' => 'decimal:2',
        'rule_results' => 'array',
    ];

    public function batch()
    {
        return $this->belongsTo(ReconciliationBatch::class, 'batch_id');
    }

    public function matchingSet()
    {
        return $this->belongsTo(MatchingSet::class);
    }

    public function leftSource()
    {
        return $this->belongsTo(Source::class, 'left_source_id');
    }

    public function rightSource()
    {
        return $this->belongsTo(Source::class, 'right_source_id');
    }

    public function visibleToSource()
    {
        return $this->belongsTo(Source::class, 'visible_to_source_id');
    }
}