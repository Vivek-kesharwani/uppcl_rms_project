<?php

namespace App\Services\Reconciliation;

use App\Models\ReconciliationBatch;
use App\Models\ReconciliationResult;

class ReconciliationEngineService
{
    public function __construct(
        private MatchingSetResolverService $resolver,
        private MatchingEngineService $matchingEngine,
        private MultiPassMatchingService $multiPassMatching,
        private RuleEngineService $ruleEngine,
        private TruthMatrixService $truthMatrix,
        private ResultGenerationService $resultService,
        private ExceptionGenerationService $exceptionService,
        private ExceptionClassificationService $exceptionClassifier
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

            $matchedRightIds = [];

            foreach ($records['left_records'] as $leftRecord) {
                $rightRecord = $this->multiPassMatching->findMatch(
                    $leftRecord,
                    $records['right_records']
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

                    $exceptions++;
                    continue;
                }

                $matchedRightIds[$rightRecord->id] = true;

                $evaluation = $this->ruleEngine->evaluate(
                    $workItem['matching_set_id'],
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

            foreach ($records['right_records'] as $rightRecord) {
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
