<?php

namespace App\Services\Reconciliation;

class ExceptionGenerationService
{
    public function missingRight(array $workItem): array
    {
        return [
            'result_status' => 'EXCEPTION',
            'exception_code' => 'MISSING_IN_' . $workItem['right_source_type'],
            'remarks' => $workItem['right_source_name'] . ' record not found for matching transaction.',
        ];
    }

    public function missingLeft(array $workItem): array
    {
        return [
            'result_status' => 'EXCEPTION',
            'exception_code' => 'MISSING_IN_' . $workItem['left_source_type'],
            'remarks' => $workItem['left_source_name'] . ' record not found for matching transaction.',
        ];
    }
}