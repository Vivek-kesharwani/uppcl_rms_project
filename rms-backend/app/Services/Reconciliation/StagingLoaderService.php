<?php

namespace App\Services\Reconciliation;

use App\Models\SourceFile;
use App\Models\ReconciliationBatch;
use App\Models\StagingTransaction;

class StagingLoaderService
{
    public function __construct(
        private CsvReaderService $csvReader,
        private ChunkManagerService $chunkManager,
        private DataCleaningService $cleaner,
        private DataValidationService $validator,
        private BulkInsertService $bulkInsert
    ) {}

    public function load(SourceFile $sourceFile, ReconciliationBatch $batch): void
    {
        $sourceFile->update([
            'status' => 'CLEANING',
        ]);

        StagingTransaction::where('source_file_id', $sourceFile->id)->delete();
        
        $total = 0;
        $failed = 0;

        $rows = $this->csvReader->read($sourceFile->file_path);

        foreach ($this->chunkManager->chunk($rows, 5000) as $chunk) {
            $insertRows = [];

            foreach ($chunk as $row) {
                $clean = $this->cleaner->clean($row);
                $validation = $this->validator->validate($clean);

                if (!$validation['is_valid']) {
                    $failed++;
                }

                $insertRows[] = [
                    'batch_id' => $batch->id,
                    'source_id' => $sourceFile->source_id,
                    'source_file_id' => $sourceFile->id,

                    'transaction_id' => $clean['transaction_id'],
                    'consumer_number' => $clean['consumer_number'],
                    'account_number' => $clean['account_number'],
                    'amount' => $clean['amount'],

                    'transaction_date' => $clean['transaction_date'],
                    'transaction_time' => $clean['transaction_time'],

                    'transaction_status' => $clean['transaction_status'],

                    'settlement_ref' => $clean['settlement_ref'],
                    'utr_number' => $clean['utr_number'],
                    'settlement_date' => $clean['settlement_date'],
                    'settlement_amount' => $clean['settlement_amount'],

                    'period_type' => $sourceFile->file_type,
                    'business_date' => $sourceFile->business_date,
                    'business_month' => $sourceFile->business_month,

                    'cleaning_status' => $validation['is_valid'] ? 'CLEANED' : 'INVALID',
                    'validation_errors' => empty($validation['errors'])
                        ? null
                        : json_encode($validation['errors']),

                    'raw_payload' => $clean['raw_payload'],

                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $total++;
            }

            $this->bulkInsert->insert($insertRows);
        }

        $sourceFile->update([
            'status' => 'STAGED',
            'total_records' => $total,
            'processed_records' => $total - $failed,
            'failed_records' => $failed,
            'processing_completed_at' => now(),
        ]);
    }
}