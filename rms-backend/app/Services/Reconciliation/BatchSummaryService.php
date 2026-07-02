<?php

namespace App\Services\Reconciliation;

use App\Models\ReconciliationBatch;
use App\Models\ReconciliationResult;
use App\Models\SourceFile;
use App\Models\StagingTransaction;

class BatchSummaryService
{
    public function summarize(ReconciliationBatch $batch): array
    {
        $sourceFilesQuery = SourceFile::query();

        if ($batch->batch_type === 'DAILY') {
            $sourceFilesQuery->whereDate('business_date', $batch->business_date);
        }

        if ($batch->batch_type === 'MONTHLY') {
            $sourceFilesQuery->where('business_month', $batch->business_month);
        }

        $totalFiles = $sourceFilesQuery->count();

        $readyFiles = (clone $sourceFilesQuery)
            ->where('status', 'STAGED')
            ->count();

        $totalRecords = StagingTransaction::where('batch_id', $batch->id)->count();

        $matchedRecords = ReconciliationResult::where('batch_id', $batch->id)
            ->where('result_status', 'MATCHED')
            ->count();

        $exceptionRecords = ReconciliationResult::where('batch_id', $batch->id)
            ->where('result_status', 'EXCEPTION')
            ->count();

        $batch->update([
            'total_files' => $totalFiles,
            'ready_files' => $readyFiles,
            'total_records' => $totalRecords,
            'matched_records' => $matchedRecords,
            'exception_records' => $exceptionRecords,
        ]);

        return [
            'batch_id' => $batch->id,
            'total_files' => $totalFiles,
            'ready_files' => $readyFiles,
            'total_records' => $totalRecords,
            'matched_records' => $matchedRecords,
            'exception_records' => $exceptionRecords,
        ];
    }
}