<?php

namespace App\Services\Reconciliation;

use App\Models\BatchFile;
use App\Models\MatchingSet;
use App\Models\ReconciliationBatch;
use App\Models\ReconciliationResult;
use App\Models\SourceFile;
use App\Models\StagingTransaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SelectedFileReconciliationService
{
    public function __construct(
        private StagingLoaderService $stagingLoader,
        private MultiPassMatchingService $multiPassMatching,
        private RuleEngineService $ruleEngine,
        private TruthMatrixService $truthMatrix,
        private ResultGenerationService $resultService,
        private ExceptionGenerationService $exceptionService,
        private ExceptionClassificationService $exceptionClassifier
    ) {}

    public function run(
        ReconciliationBatch $batch,
        MatchingSet $matchingSet,
        SourceFile $leftFile,
        SourceFile $rightFile
    ): array {
        return DB::transaction(function () use ($batch, $matchingSet, $leftFile, $rightFile) {

            /*
            |--------------------------------------------------------------------------
            | 1. Validate File Selection
            |--------------------------------------------------------------------------
            */

            $this->validateSelectedFiles($matchingSet, $leftFile, $rightFile);

            /*
            |--------------------------------------------------------------------------
            | 2. Start Batch
            |--------------------------------------------------------------------------
            */

            $batch->update([
                'status' => 'RECONCILING',
                'run_mode' => $batch->run_mode ?? 'MANUAL',
                'triggered_by' => auth()->id(),
                'started_at' => now(),
                'completed_at' => null,
                'error_message' => null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 3. Register Files Used In Batch
            |--------------------------------------------------------------------------
            */

            $this->registerBatchFiles($batch, $matchingSet, $leftFile, $rightFile);

            /*
            |--------------------------------------------------------------------------
            | 4. Stage Selected Files
            |--------------------------------------------------------------------------
            */

            $this->stagingLoader->load($leftFile, $batch);
            $this->stagingLoader->load($rightFile, $batch);

            /*
            |--------------------------------------------------------------------------
            | 5. Clear Previous Results For Same Batch + Matching Set
            |--------------------------------------------------------------------------
            */

            ReconciliationResult::where('batch_id', $batch->id)
                ->where('matching_set_id', $matchingSet->id)
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | 6. Prepare Work Item
            |--------------------------------------------------------------------------
            */

            $workItem = [
                'batch_id' => $batch->id,
                'matching_set_id' => $matchingSet->id,
                'matching_set_code' => $matchingSet->set_code ?? null,

                'left_source_id' => $leftFile->source_id,
                'right_source_id' => $rightFile->source_id,

                'left_source_type' => $leftFile->source->source_type ?? 'LEFT',
                'right_source_type' => $rightFile->source->source_type ?? 'RIGHT',

                'left_source_name' => $leftFile->source->source_name ?? 'Left Source',
                'right_source_name' => $rightFile->source->source_name ?? 'Right Source',

                'left_file_id' => $leftFile->id,
                'right_file_id' => $rightFile->id,

                'period_type' => $leftFile->file_type,
                'business_date' => $leftFile->business_date,
                'business_month' => $leftFile->business_month,
            ];

            /*
            |--------------------------------------------------------------------------
            | 7. Fetch Cleaned Staging Records
            |--------------------------------------------------------------------------
            */

            $leftRecords = StagingTransaction::where('batch_id', $batch->id)
                ->where('source_file_id', $leftFile->id)
                ->where('cleaning_status', 'CLEANED')
                ->get();

            $rightRecords = StagingTransaction::where('batch_id', $batch->id)
                ->where('source_file_id', $rightFile->id)
                ->where('cleaning_status', 'CLEANED')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | 8. Run Matching
            |--------------------------------------------------------------------------
            */

            $matchedRightIds = [];

            foreach ($leftRecords as $leftRecord) {

                $rightRecord = $this->multiPassMatching->findMatch(
                    $leftRecord,
                    $rightRecords
                );

                if (!$rightRecord) {

                    $decision = $this->exceptionClassifier->classify(
                        $leftRecord,
                        null,
                        $this->exceptionService->missingRight($workItem)
                    );

                    $this->resultService->storeResult(
                        $workItem,
                        $leftRecord,
                        null,
                        ['results' => []],
                        $decision
                    );

                    continue;
                }

                $matchedRightIds[$rightRecord->id] = true;

                $evaluation = $this->ruleEngine->evaluate(
                    $matchingSet->id,
                    $leftRecord,
                    $rightRecord
                );

                $decision = $this->truthMatrix->decide($evaluation);

                $decision = $this->exceptionClassifier->classify(
                    $leftRecord,
                    $rightRecord,
                    $decision
                );

                $this->resultService->storeResult(
                    $workItem,
                    $leftRecord,
                    $rightRecord,
                    $evaluation,
                    $decision
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 9. Detect Right-Side Unmatched Records
            |--------------------------------------------------------------------------
            */

            foreach ($rightRecords as $rightRecord) {

                if (isset($matchedRightIds[$rightRecord->id])) {
                    continue;
                }

                $decision = $this->exceptionClassifier->classify(
                    null,
                    $rightRecord,
                    $this->exceptionService->missingLeft($workItem)
                );

                $this->resultService->storeResult(
                    $workItem,
                    null,
                    $rightRecord,
                    ['results' => []],
                    $decision
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 10. Mark Batch Files As Processed
            |--------------------------------------------------------------------------
            */

            BatchFile::where('batch_id', $batch->id)
                ->whereIn('source_file_id', [$leftFile->id, $rightFile->id])
                ->update([
                    'status' => 'PROCESSED',
                    'processed_at' => now(),
                ]);

            /*
            |--------------------------------------------------------------------------
            | 11. Mark Source Files As Reconciled
            |--------------------------------------------------------------------------
            */

            SourceFile::whereIn('id', [$leftFile->id, $rightFile->id])
                ->update([
                    'processing_status' => 'COMPLETED',
                    'reconciliation_status' => 'RECONCILED',
                    'reconciled_at' => now(),
                    'is_locked' => true,
                ]);

            /*
            |--------------------------------------------------------------------------
            | 12. Refresh Batch Statistics Dynamically
            |--------------------------------------------------------------------------
            */

            $this->refreshBatchStatistics($batch);

            $batch->refresh();

            return [
                'batch_id' => $batch->id,
                'matching_set_id' => $matchingSet->id,
                'left_file_id' => $leftFile->id,
                'right_file_id' => $rightFile->id,
                'matched' => $batch->matched_records,
                'exceptions' => $batch->exception_records,
                'total_files' => $batch->total_files,
                'ready_files' => $batch->ready_files,
                'total_records' => $batch->total_records,
            ];
        });
    }

    private function validateSelectedFiles(
        MatchingSet $matchingSet,
        SourceFile $leftFile,
        SourceFile $rightFile
    ): void {
        if ($leftFile->file_type !== $rightFile->file_type) {
            throw new InvalidArgumentException('Selected files must have the same file type.');
        }

        if ($leftFile->file_type === 'DAILY') {
            if (
                !$leftFile->business_date ||
                !$rightFile->business_date ||
                !$leftFile->business_date->isSameDay($rightFile->business_date)
            ) {
                throw new InvalidArgumentException('Daily files must have the same business date.');
            }
        }

        if ($leftFile->file_type === 'MONTHLY') {
            if ($leftFile->business_month !== $rightFile->business_month) {
                throw new InvalidArgumentException('Monthly files must have the same business month.');
            }
        }

        if (($leftFile->source->source_type ?? null) !== $matchingSet->left_source_type) {
            throw new InvalidArgumentException('Left file does not match the selected matching set.');
        }

        if (($rightFile->source->source_type ?? null) !== $matchingSet->right_source_type) {
            throw new InvalidArgumentException('Right file does not match the selected matching set.');
        }
    }

    private function registerBatchFiles(
        ReconciliationBatch $batch,
        MatchingSet $matchingSet,
        SourceFile $leftFile,
        SourceFile $rightFile
    ): void {
        BatchFile::updateOrCreate(
            [
                'batch_id' => $batch->id,
                'source_file_id' => $leftFile->id,
                'file_side' => 'LEFT',
            ],
            [
                'source_id' => $leftFile->source_id,
                'matching_set_id' => $matchingSet->id,
                'file_role' => 'PRIMARY',
                'status' => 'SELECTED',
                'selected_at' => now(),
                'error_message' => null,
            ]
        );

        BatchFile::updateOrCreate(
            [
                'batch_id' => $batch->id,
                'source_file_id' => $rightFile->id,
                'file_side' => 'RIGHT',
            ],
            [
                'source_id' => $rightFile->source_id,
                'matching_set_id' => $matchingSet->id,
                'file_role' => 'COMPARISON',
                'status' => 'SELECTED',
                'selected_at' => now(),
                'error_message' => null,
            ]
        );

        SourceFile::whereIn('id', [$leftFile->id, $rightFile->id])
            ->update([
                'reconciliation_status' => 'IN_BATCH',
                'is_locked' => true,
            ]);
    }

    private function refreshBatchStatistics(ReconciliationBatch $batch): void
    {
        $totalFiles = $batch->batchFiles()->count();

        $readyFiles = $batch->batchFiles()
            ->where('status', 'PROCESSED')
            ->count();

        $totalRecords = $batch->batchFiles()->sum('total_records');

        $matchedRecords = ReconciliationResult::where('batch_id', $batch->id)
            ->where('result_status', 'MATCHED')
            ->count();

        $exceptionRecords = ReconciliationResult::where('batch_id', $batch->id)
            ->where('result_status', 'EXCEPTION')
            ->count();

        $batch->update([
            'status' => 'COMPLETED',
            'total_files' => $totalFiles,
            'ready_files' => $readyFiles,
            'total_records' => $totalRecords,
            'matched_records' => $matchedRecords,
            'exception_records' => $exceptionRecords,
            'completed_at' => now(),
        ]);
    }
}