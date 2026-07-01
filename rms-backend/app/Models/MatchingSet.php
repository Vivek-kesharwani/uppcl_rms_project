<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchingSet extends Model
{
    protected $fillable = [
        'set_code',
        'set_name',
        'left_source_type',
        'right_source_type',
        'period_type',
        'execution_order',
        'can_run_parallel',
        'is_active',
        'description',
    ];

    protected $casts = [
        'can_run_parallel' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function rules()
    {
        return $this->hasMany(MatchingRule::class);
    }

    public function reconciliationResults()
    {
        return $this->hasMany(ReconciliationResult::class);
    }
}