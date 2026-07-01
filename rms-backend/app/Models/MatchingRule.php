<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchingRule extends Model
{
    protected $fillable = [
        'matching_set_id',
        'rule_code',
        'rule_name',
        'rule_group',
        'left_field',
        'right_field',
        'comparison_operator',
        'tolerance_value',
        'priority',
        'is_mandatory',
        'stop_on_failure',
        'is_active',
    ];

    protected $casts = [
        'tolerance_value' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'stop_on_failure' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function matchingSet()
    {
        return $this->belongsTo(MatchingSet::class);
    }
}