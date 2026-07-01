<?php

namespace App\Services\Reconciliation;

use App\Models\MatchingSet;
use App\Models\ReconciliationBatch;
use App\Models\Source;
use Illuminate\Support\Collection;

class MatchingSetResolverService
{
    public function resolve(ReconciliationBatch $batch): Collection
    {
        $matchingSets = MatchingSet::where('is_active', true)
            ->orderBy('execution_order')
            ->get();

        $workItems = collect();

        foreach ($matchingSets as $matchingSet) {
            $leftSources = Source::where('source_type', $matchingSet->left_source_type)
                ->where('is_active', true)
                ->get();

            $rightSources = Source::where('source_type', $matchingSet->right_source_type)
                ->where('is_active', true)
                ->get();

            foreach ($leftSources as $leftSource) {
                foreach ($rightSources as $rightSource) {
                    $workItems->push([
                        'batch_id' => $batch->id,
                        'matching_set_id' => $matchingSet->id,
                        'matching_set_code' => $matchingSet->set_code,
                        'left_source_id' => $leftSource->id,
                        'left_source_name' => $leftSource->source_name,
                        'left_source_type' => $leftSource->source_type,
                        'right_source_id' => $rightSource->id,
                        'right_source_name' => $rightSource->source_name,
                        'right_source_type' => $rightSource->source_type,
                        'period_type' => $batch->batch_type,
                        'business_date' => $batch->business_date,
                        'business_month' => $batch->business_month,
                    ]);
                }
            }
        }

        return $workItems;
    }
}