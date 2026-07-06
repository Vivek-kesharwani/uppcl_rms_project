<?php

namespace App\Services\Reconciliation;

use App\Models\ReconciliationBatch;
use App\Models\ReconciliationResult;
use App\Models\ReconciliationResultFile;
use Illuminate\Support\Facades\File;

class ResultFileGenerationService
{
    public function generate(ReconciliationBatch $batch): ReconciliationResultFile
    {
        $matchingSetId = ReconciliationResult::where('batch_id', $batch->id)
            ->value('matching_set_id');

        $folder = storage_path('rms/results/' . $batch->batch_code);

        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        $fileName = 'result_' . $batch->batch_code . '.csv';
        $filePath = $folder . DIRECTORY_SEPARATOR . $fileName;

        $handle = fopen($filePath, 'w');

        fputcsv($handle, [
            'Batch Code',
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
            ->chunk(5000, function ($results) use ($handle, $batch) {
                foreach ($results as $result) {
                    fputcsv($handle, [
                        $batch->batch_code,
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

        return ReconciliationResultFile::create([
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
    }
}