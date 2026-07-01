<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceFile extends Model
{
    protected $fillable = [
        'source_id',
        'file_name',
        'file_path',
        'file_type',
        'business_date',
        'business_month',
        'file_size',
        'checksum',
        'status',
        'total_records',
        'processed_records',
        'failed_records',
        'received_at',
        'processing_started_at',
        'processing_completed_at',
        'error_message',
    ];

    protected $casts = [
        'business_date' => 'date',
        'received_at' => 'datetime',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
    ];

    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}