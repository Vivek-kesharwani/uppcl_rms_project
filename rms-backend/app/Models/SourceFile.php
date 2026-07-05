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
        'file_status',
        'processing_status',
        'reconciliation_status',

        'total_records',
        'valid_records',
        'invalid_records',
        'duplicate_records',
        'processed_records',
        'failed_records',

        'uploaded_by',
        'uploaded_ip',

        'received_at',
        'validated_at',
        'staged_at',
        'processing_started_at',
        'processing_completed_at',
        'reconciled_at',

        'is_locked',
        'error_message',
    ];

    protected $casts = [
        'business_date' => 'date',

        'received_at' => 'datetime',
        'validated_at' => 'datetime',
        'staged_at' => 'datetime',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
        'reconciled_at' => 'datetime',

        'is_locked' => 'boolean',
    ];

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function batchFiles()
    {
        return $this->hasMany(BatchFile::class);
    }
}