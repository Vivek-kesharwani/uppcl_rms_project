<?php

namespace App\Services\Reconciliation;

class ExceptionClassificationService
{
    public function classify(
        ?object $leftRecord,
        ?object $rightRecord,
        array $decision
    ): array {

        if ($decision['result_status'] === 'MATCHED') {
            return [
                'result_status' => 'MATCHED',
                'exception_code' => null,
                'remarks' => 'All configured rules passed.',
            ];
        }

        if (!$leftRecord && $rightRecord) {
            return [
                'result_status' => 'EXCEPTION',
                'exception_code' => 'MISSING_IN_AGENCY',
                'remarks' => 'Source record missing.',
            ];
        }

        if ($leftRecord && !$rightRecord) {
            return [
                'result_status' => 'EXCEPTION',
                'exception_code' => 'MISSING_IN_TARGET',
                'remarks' => 'Matching record not found.',
            ];
        }

        if (
            isset($decision['exception_code']) &&
            $decision['exception_code'] === 'AMOUNT_MISMATCH'
        ) {
            return [
                'result_status' => 'EXCEPTION',
                'exception_code' => 'AMOUNT_MISMATCH',
                'remarks' => 'Amount mismatch detected.',
            ];
        }

        return [
            'result_status' => 'EXCEPTION',
            'exception_code' => 'RULE_FAILED',
            'remarks' => 'Rule evaluation failed.',
        ];
    }
}