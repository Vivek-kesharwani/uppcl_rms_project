<?php

namespace App\Services\Reconciliation;

use App\Models\ReconciliationResult;
use App\Models\StagingTransaction;

class ResultGenerationService
{
    public function storeResult(
        array $workItem,
        ?StagingTransaction $leftRecord,
        ?StagingTransaction $rightRecord,
        array $ruleEvaluation,
        array $decision
    ): ReconciliationResult {
        return ReconciliationResult::create([
            'batch_id' => $workItem['batch_id'],
            'matching_set_id' => $workItem['matching_set_id'],

            'left_source_id' => $workItem['left_source_id'],
            'right_source_id' => $workItem['right_source_id'],

            'left_record_id' => $leftRecord?->id,
            'right_record_id' => $rightRecord?->id,

            'transaction_id' => $leftRecord?->transaction_id
                ?? $rightRecord?->transaction_id,

            'consumer_number' => $leftRecord?->consumer_number
                ?? $rightRecord?->consumer_number,

            'settlement_ref' => $leftRecord?->settlement_ref
                ?? $rightRecord?->settlement_ref,

            'utr_number' => $leftRecord?->utr_number
                ?? $rightRecord?->utr_number,

            'period_type' => $workItem['period_type'],
            'business_date' => $workItem['business_date'],
            'business_month' => $workItem['business_month'],

            'result_status' => $decision['result_status'],
            'exception_code' => $decision['exception_code'],
            'variance_amount' => 0.00,

            'visible_to_source_id' => $workItem['left_source_id'],

            'rule_results' => $ruleEvaluation['results'] ?? [],
            'remarks' => $decision['remarks'],
        ]);
    }
}