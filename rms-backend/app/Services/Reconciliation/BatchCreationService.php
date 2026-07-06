<?php

namespace App\Services\Reconciliation;

use App\Models\MatchingSet;
use App\Models\ReconciliationBatch;
use App\Models\SourceFile;
use Illuminate\Support\Str;

class BatchCreationService
{
    public function create(
        MatchingSet $matchingSet,
        SourceFile $leftFile,
        SourceFile $rightFile
    ): ReconciliationBatch {
        $batchCode =
            $matchingSet->set_code . '_' .
            $leftFile->file_type . '_' .
            now()->format('Ymd_His') . '_' .
            strtoupper(Str::random(5));

        return ReconciliationBatch::create([
            'batch_code' => $batchCode,
            'batch_type' => $leftFile->file_type,
            'business_date' => $leftFile->business_date,
            'business_month' => $leftFile->business_month,
            'status' => 'CREATED',
            'run_mode' => 'MANUAL',
            'triggered_by' => auth()->id(),
            'total_files' => 0,
            'ready_files' => 0,
            'total_records' => 0,
            'matched_records' => 0,
            'exception_records' => 0,
        ]);
    }
}