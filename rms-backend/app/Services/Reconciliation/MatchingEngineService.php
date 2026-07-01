<?php

namespace App\Services\Reconciliation;

use App\Models\StagingTransaction;
use Illuminate\Support\Collection;

class MatchingEngineService
{
    public function getRecordsForWorkItem(array $workItem): array
    {
        $leftRecords = StagingTransaction::where('batch_id', $workItem['batch_id'])
            ->where('source_id', $workItem['left_source_id'])
            ->where('period_type', $workItem['period_type'])
            ->where('cleaning_status', 'CLEANED')
            ->when($workItem['period_type'] === 'DAILY', function ($query) use ($workItem) {
                $query->whereDate('business_date', $workItem['business_date']);
            })
            ->when($workItem['period_type'] === 'MONTHLY', function ($query) use ($workItem) {
                $query->where('business_month', $workItem['business_month']);
            })
            ->get();

        $rightRecords = StagingTransaction::where('batch_id', $workItem['batch_id'])
            ->where('source_id', $workItem['right_source_id'])
            ->where('period_type', $workItem['period_type'])
            ->where('cleaning_status', 'CLEANED')
            ->when($workItem['period_type'] === 'DAILY', function ($query) use ($workItem) {
                $query->whereDate('business_date', $workItem['business_date']);
            })
            ->when($workItem['period_type'] === 'MONTHLY', function ($query) use ($workItem) {
                $query->where('business_month', $workItem['business_month']);
            })
            ->get();

        return [
            'left_records' => $leftRecords,
            'right_records' => $rightRecords,
        ];
    }
}