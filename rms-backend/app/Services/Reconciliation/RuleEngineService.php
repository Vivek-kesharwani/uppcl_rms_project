<?php

namespace App\Services\Reconciliation;

use App\Models\MatchingRule;
use App\Models\StagingTransaction;

class RuleEngineService
{
    public function evaluate(
        int $matchingSetId,
        StagingTransaction $leftRecord,
        StagingTransaction $rightRecord
    ): array {
        $rules = MatchingRule::where('matching_set_id', $matchingSetId)
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();

        $results = [];
        $overallPassed = true;

        foreach ($rules as $rule) {
            $leftValue = $leftRecord->{$rule->left_field};
            $rightValue = $rightRecord->{$rule->right_field};

            $passed = $this->compare(
                $leftValue,
                $rightValue,
                $rule->comparison_operator,
                $rule->tolerance_value
            );

            $results[] = [
                'rule_code' => $rule->rule_code,
                'rule_name' => $rule->rule_name,
                'left_field' => $rule->left_field,
                'right_field' => $rule->right_field,
                'left_value' => $leftValue,
                'right_value' => $rightValue,
                'operator' => $rule->comparison_operator,
                'passed' => $passed,
            ];

            if (!$passed && $rule->is_mandatory) {
                $overallPassed = false;

                if ($rule->stop_on_failure) {
                    break;
                }
            }
        }

        return [
            'passed' => $overallPassed,
            'results' => $results,
        ];
    }

    private function compare($leftValue, $rightValue, string $operator, $tolerance = null): bool
    {
        return match ($operator) {
            'EQUAL', 'STATUS_EQUAL' => (string) $leftValue === (string) $rightValue,

            'AMOUNT_EQUAL' => number_format((float) $leftValue, 2, '.', '') ===
                              number_format((float) $rightValue, 2, '.', ''),

            'DATE_EQUAL' => date('Y-m-d', strtotime($leftValue)) ===
                            date('Y-m-d', strtotime($rightValue)),

            'TIME_EQUAL' => date('H:i:s', strtotime($leftValue)) ===
                            date('H:i:s', strtotime($rightValue)),

            'TOLERANCE' => abs((float) $leftValue - (float) $rightValue) <= (float) $tolerance,

            default => false,
        };
    }
}