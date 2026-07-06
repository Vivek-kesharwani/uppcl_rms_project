<?php

namespace App\Services\Reconciliation;

use App\Models\ExceptionRecord;
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
        $result = ReconciliationResult::create([
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
            'variance_amount' => $decision['variance_amount'] ?? 0.00,

            'visible_to_source_id' => $workItem['left_source_id'],

            'rule_results' => $ruleEvaluation['results'] ?? [],
            'remarks' => $decision['remarks'] ?? null,
        ]);

        if ($result->result_status === 'EXCEPTION') {
            $this->createExceptionCase($result);
        }

        return $result;
    }

    private function createExceptionCase(ReconciliationResult $result): void
    {
        ExceptionRecord::updateOrCreate(
            [
                'reconciliation_result_id' => $result->id,
            ],
            [
                'case_number' => 'EXC-' . now()->format('Ymd') . '-' . str_pad($result->id, 6, '0', STR_PAD_LEFT),

                'txn_id' => $result->transaction_id,
                'exception_code' => $result->exception_code,
                'severity' => $this->severityFor($result->exception_code),
                'priority' => $this->priorityFor($result->exception_code),
                'variance_amount' => $result->variance_amount,

                'status' => 'OPEN',
                'visible_to_source_id' => $result->visible_to_source_id ?? null,

                'opened_at' => now(),
                'remarks' => $result->remarks,
                'sla_due_at' => now()->addHours(24),
                'sla_breached' => false,
            ]
        );
    }

    private function severityFor(?string $exceptionCode): string
    {
        return match ($exceptionCode) {
            'AMOUNT_MISMATCH' => 'HIGH',
            'MISSING_IN_AGENCY',
            'MISSING_IN_TARGET',
            'MISSING_SETTLEMENT' => 'MEDIUM',
            default => 'LOW',
        };
    }

    private function priorityFor(?string $exceptionCode): string
    {
        return match ($exceptionCode) {
            'AMOUNT_MISMATCH' => 'HIGH',
            'MISSING_IN_AGENCY',
            'MISSING_IN_TARGET',
            'MISSING_SETTLEMENT' => 'MEDIUM',
            default => 'LOW',
        };
    }
}