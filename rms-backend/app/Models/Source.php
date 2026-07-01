<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $fillable = [
        'source_type',
        'source_name',
        'display_name',
        'daily_folder_path',
        'monthly_folder_path',
        'daily_file_pattern',
        'monthly_file_pattern',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sourceFiles()
    {
        return $this->hasMany(SourceFile::class);
    }

    public function stagingTransactions()
    {
        return $this->hasMany(StagingTransaction::class);
    }
}