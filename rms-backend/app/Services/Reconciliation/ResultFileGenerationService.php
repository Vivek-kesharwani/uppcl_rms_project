<?php

namespace App\Services\Reconciliation;

use App\Models\BatchFile;
use App\Models\ReconciliationBatch;
use App\Models\ReconciliationResult;
use App\Models\ReconciliationResultFile;
use App\Services\Audit\AuditLogService;
use Illuminate\Support\Facades\File;

class ResultFileGenerationService
{
    public function __construct(
        private AuditLogService $auditLog
    ) {}

    public function generate(ReconciliationBatch $batch): ReconciliationResultFile
    {
        $matchingSetId = ReconciliationResult::where('batch_id', $batch->id)
            ->value('matching_set_id');

        $leftBatchFile = BatchFile::with('sourceFile.source')
            ->where('batch_id', $batch->id)
            ->where('file_side', 'LEFT')
            ->first();

        $rightBatchFile = BatchFile::with('sourceFile.source')
            ->where('batch_id', $batch->id)
            ->where('file_side', 'RIGHT')
            ->first();

        $leftName = $this->safeName($leftBatchFile?->sourceFile?->source?->source_name ?? 'Left');
        $rightName = $this->safeName($rightBatchFile?->sourceFile?->source?->source_name ?? 'Right');

        $period = strtolower($batch->batch_type);

        $datePart = $batch->batch_type === 'DAILY'
            ? ($batch->business_date ? $batch->business_date->format('dmY') : now()->format('dmY'))
            : ($batch->business_month ?: now()->format('mY'));

        $fileName = "Result_{$leftName}_vs_{$rightName}_{$period}_{$datePart}.csv";

        $folder = storage_path("rms/results/{$leftName}_vs_{$rightName}/{$period}/{$datePart}");

        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        $filePath = $folder . DIRECTORY_SEPARATOR . $fileName;

        $handle = fopen($filePath, 'w');

        fputcsv($handle, [
            'Batch Code',
            'Result File Name',
            'Transaction ID',
            'Consumer Number',
            'Settlement Ref',
            'UTR Number',
            'Result Status',
            'Exception Code',
            'Variance Amount',
            'Remarks',
            'Business Date',
            'Business Month',
        ]);

        ReconciliationResult::where('batch_id', $batch->id)
            ->orderBy('id')
            ->chunk(5000, function ($results) use ($handle, $batch, $fileName) {
                foreach ($results as $result) {
                    fputcsv($handle, [
                        $batch->batch_code,
                        $fileName,
                        $result->transaction_id,
                        $result->consumer_number,
                        $result->settlement_ref,
                        $result->utr_number,
                        $result->result_status,
                        $result->exception_code,
                        $result->variance_amount,
                        $result->remarks,
                        optional($result->business_date)->format('Y-m-d'),
                        $result->business_month,
                    ]);
                }
            });

        fclose($handle);

        $resultFile = ReconciliationResultFile::create([
            'batch_id' => $batch->id,
            'matching_set_id' => $matchingSetId,
            'result_type' => 'CSV',
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => File::size($filePath),
            'total_records' => $batch->total_records,
            'matched_records' => $batch->matched_records,
            'exception_records' => $batch->exception_records,
            'business_date' => $batch->business_date,
            'business_month' => $batch->business_month,
            'status' => 'READY',
            'generated_at' => now(),
        ]);

        $this->auditLog->log(
            'GENERATE_RESULT',
            'RESULT_FILE',
            'Generated ' . $fileName
        );

        return $resultFile;
    }

    private function safeName(string $name): string
    {
        return preg_replace('/[^A-Za-z0-9_]/', '_', trim($name));
    }
}