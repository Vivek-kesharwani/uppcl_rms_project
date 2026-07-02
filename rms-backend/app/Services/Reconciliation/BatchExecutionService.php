<?php

namespace App\Services\Reconciliation;

use App\Models\ReconciliationBatch;
use App\Models\SourceFile;
use Throwable;

class BatchExecutionService
{
    public function __construct(
        private FolderScannerService $scanner,
        private FileRegistrationService $registration,
        private FileValidationService $validator,
        private StagingLoaderService $loader,
        private ReconciliationEngineService $engine
    ) {}

    public function execute(ReconciliationBatch $batch): array
    {
        try {
            $filesProcessed = 0;

            $detectedFiles = $this->scanner->scan();

            foreach ($detectedFiles as $detectedFile) {
                $this->registration->register($detectedFile);
            }

            $sourceFiles = SourceFile::where(function ($query) use ($batch) {
                    if ($batch->batch_type === 'DAILY') {
                        $query->whereDate('business_date', $batch->business_date);
                    }

                    if ($batch->batch_type === 'MONTHLY') {
                        $query->where('business_month', $batch->business_month);
                    }
                })
                ->get();

            foreach ($sourceFiles as $sourceFile) {
                if (!$this->validator->validate($sourceFile)) {
                    continue;
                }

                $this->loader->load($sourceFile, $batch);
                $filesProcessed++;
            }

            $result = $this->engine->run($batch);

            return [
                'batch_id' => $batch->id,
                'files_detected' => count($detectedFiles),
                'files_processed' => $filesProcessed,
                'matched' => $result['matched'],
                'exceptions' => $result['exceptions'],
            ];
        } catch (Throwable $e) {
            $batch->update([
                'status' => 'FAILED',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }
}