<?php

namespace App\Services\Reconciliation;

use App\Models\ReconciliationBatch;
use App\Models\ReconciliationResult;

class ReconciliationEngineService
{
    public function __construct(
        private MatchingSetResolverService $resolver,
        private MatchingEngineService $matchingEngine,
        private RuleEngineService $ruleEngine,
        private TruthMatrixService $truthMatrix,
        private ResultGenerationService $resultService
    ) {}

    public function run(ReconciliationBatch $batch): array
    {
        $batch->update([
            'status' => 'RECONCILING',
            'started_at' => now(),
        ]);

        ReconciliationResult::where('batch_id', $batch->id)->delete();

        $matched = 0;
        $exceptions = 0;

        $workItems = $this->resolver->resolve($batch);

        foreach ($workItems as $workItem) {
            $records = $this->matchingEngine->getRecordsForWorkItem($workItem);

            $rightIndex = $records['right_records']->keyBy('transaction_id');

            foreach ($records['left_records'] as $leftRecord) {
                $rightRecord = $rightIndex->get($leftRecord->transaction_id);

                if (!$rightRecord) {
                    $exceptions++;
                    continue;
                }

                $evaluation = $this->ruleEngine->evaluate(
                    $workItem['matching_set_id'],
                    $leftRecord,
                    $rightRecord
                );

                $decision = $this->truthMatrix->decide($evaluation);

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
        }

        $batch->update([
            'status' => 'COMPLETED',
            'matched_records' => $matched,
            'exception_records' => $exceptions,
            'completed_at' => now(),
        ]);

        return [
            'batch_id' => $batch->id,
            'matched' => $matched,
            'exceptions' => $exceptions,
        ];
    }
}