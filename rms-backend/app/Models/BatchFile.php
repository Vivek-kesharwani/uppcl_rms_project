<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchFile extends Model
{
    protected $fillable = [

        'batch_id',

        'source_file_id',

        'source_id',

        'matching_set_id',

        'file_side',

        'file_role',

        'status',

        'total_records',

        'staged_records',

        'failed_records',

        'selected_at',

        'locked_at',

        'staged_at',

        'processed_at',

        'error_message',
    ];

    protected $casts = [

        'selected_at' => 'datetime',

        'locked_at' => 'datetime',

        'staged_at' => 'datetime',

        'processed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

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

    public function matchingSet()
    {
        return $this->belongsTo(MatchingSet::class);
    }
}