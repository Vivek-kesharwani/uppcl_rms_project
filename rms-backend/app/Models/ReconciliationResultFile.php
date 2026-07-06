<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReconciliationResultFile extends Model
{
    protected $fillable = [
        'batch_id',
        'matching_set_id',
        'result_type',
        'file_name',
        'file_path',
        'file_size',
        'total_records',
        'matched_records',
        'exception_records',
        'business_date',
        'business_month',
        'status',
        'generated_at',
    ];

    protected $casts = [
        'business_date' => 'date',
        'generated_at' => 'datetime',
    ];

    public function batch()
    {
        return $this->belongsTo(ReconciliationBatch::class);
    }

    public function matchingSet()
    {
        return $this->belongsTo(MatchingSet::class);
    }
}