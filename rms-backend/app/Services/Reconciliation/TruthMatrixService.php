<?php

namespace App\Services\Reconciliation;

class TruthMatrixService
{
    public function decide(array $ruleEvaluation): array
    {
        if ($ruleEvaluation['passed'] === true) {
            return [
                'result_status' => 'MATCHED',
                'exception_code' => null,
                'remarks' => 'All configured rules passed.',
            ];
        }

        $failedRules = collect($ruleEvaluation['results'])
            ->where('passed', false)
            ->pluck('rule_code')
            ->toArray();

        if (in_array('AMOUNT_MATCH', $failedRules) || in_array('SETTLEMENT_AMOUNT_MATCH', $failedRules)) {
            return [
                'result_status' => 'EXCEPTION',
                'exception_code' => 'AMOUNT_MISMATCH',
                'remarks' => 'Amount mismatch detected.',
            ];
        }

        if (in_array('DATE_MATCH', $failedRules) || in_array('SETTLEMENT_DATE_MATCH', $failedRules)) {
            return [
                'result_status' => 'EXCEPTION',
                'exception_code' => 'DATE_MISMATCH',
                'remarks' => 'Date mismatch detected.',
            ];
        }

        if (in_array('CONSUMER_MATCH', $failedRules)) {
            return [
                'result_status' => 'EXCEPTION',
                'exception_code' => 'CONSUMER_MISMATCH',
                'remarks' => 'Consumer number mismatch detected.',
            ];
        }

        if (in_array('TXN_ID_MATCH', $failedRules)) {
            return [
                'result_status' => 'UNMATCHED',
                'exception_code' => 'TRANSACTION_ID_MISMATCH',
                'remarks' => 'Transaction ID mismatch detected.',
            ];
        }

        if (in_array('SETTLEMENT_REF_MATCH', $failedRules)) {
            return [
                'result_status' => 'UNMATCHED',
                'exception_code' => 'SETTLEMENT_REF_MISMATCH',
                'remarks' => 'Settlement reference mismatch detected.',
            ];
        }

        return [
            'result_status' => 'EXCEPTION',
            'exception_code' => 'RULE_FAILED',
            'remarks' => 'One or more configured rules failed.',
        ];
    }
}