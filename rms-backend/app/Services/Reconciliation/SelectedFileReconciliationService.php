<?php

namespace App\Services\Reconciliation;

use App\Models\MatchingSet;
use App\Models\ReconciliationBatch;
use App\Models\ReconciliationResult;
use App\Models\SourceFile;
use App\Models\StagingTransaction;

class SelectedFileReconciliationService
{
    public function __construct(
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
        ReconciliationResult::where('batch_id', $batch->id)
            ->where('matching_set_id', $matchingSet->id)
            ->where(function ($query) use ($leftFile, $rightFile) {
                $query->whereIn('left_record_id', function ($sub) use ($leftFile) {
                    $sub->select('id')
                        ->from('staging_transactions')
                        ->where('source_file_id', $leftFile->id);
                })->orWhereIn('right_record_id', function ($sub) use ($rightFile) {
                    $sub->select('id')
                        ->from('staging_transactions')
                        ->where('source_file_id', $rightFile->id);
                });
            })
            ->delete();

        $workItem = [
            'batch_id' => $batch->id,
            'matching_set_id' => $matchingSet->id,
            'matching_set_code' => $matchingSet->matching_set_code,
            'left_source_id' => $leftFile->source_id,
            'right_source_id' => $rightFile->source_id,
            'left_source_type' => $leftFile->source->source_type ?? 'LEFT',
            'right_source_type' => $rightFile->source->source_type ?? 'RIGHT',
            'left_source_name' => $leftFile->source->source_name ?? 'Left Source',
            'right_source_name' => $rightFile->source->source_name ?? 'Right Source',
            'period_type' => $leftFile->file_type,
            'business_date' => $leftFile->business_date,
            'business_month' => $leftFile->business_month,
        ];

        $leftRecords = StagingTransaction::where('batch_id', $batch->id)
            ->where('source_file_id', $leftFile->id)
            ->where('cleaning_status', 'CLEANED')
            ->get();

        $rightRecords = StagingTransaction::where('batch_id', $batch->id)
            ->where('source_file_id', $rightFile->id)
            ->where('cleaning_status', 'CLEANED')
            ->get();

        $matched = 0;
        $exceptions = 0;
        $matchedRightIds = [];

        foreach ($leftRecords as $leftRecord) {
            $rightRecord = $this->multiPassMatching->findMatch($leftRecord, $rightRecords);

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

                $exceptions++;
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

            if ($decision['result_status'] === 'MATCHED') {
                $matched++;
            } else {
                $exceptions++;
            }
        }

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

            $exceptions++;
        }

        return [
            'batch_id' => $batch->id,
            'matching_set_id' => $matchingSet->id,
            'left_file_id' => $leftFile->id,
            'right_file_id' => $rightFile->id,
            'matched' => $matched,
            'exceptions' => $exceptions,
        ];
    }
}