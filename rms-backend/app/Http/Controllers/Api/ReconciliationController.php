<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchingSet;
use App\Models\ReconciliationBatch;
use App\Models\SourceFile;
use App\Services\Reconciliation\BatchExecutionService;
use App\Services\Reconciliation\SelectedFileReconciliationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReconciliationController extends Controller
{
    public function run(ReconciliationBatch $batch, BatchExecutionService $executor): JsonResponse
    {
        $summary = $executor->execute($batch);

        return response()->json([
            'status' => 'success',
            'message' => 'Reconciliation batch executed successfully.',
            'data' => $summary,
        ]);
    }

    public function matchingSets(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => MatchingSet::where('is_active', true)->get(),
        ]);
    }

    public function filesForMatchingSet(MatchingSet $matchingSet): JsonResponse
    {
        $leftFiles = SourceFile::whereHas('source', function ($query) use ($matchingSet) {
            $query->where('source_type', $matchingSet->left_source_type);
        })
        ->orderByDesc('business_date')
        ->get();
        
        $rightFiles = SourceFile::whereHas('source', function ($query) use ($matchingSet) {
            $query->where('source_type', $matchingSet->right_source_type);
        })
        ->orderByDesc('business_date')
        ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'matching_set' => $matchingSet,
                'left_files' => $leftFiles,
                'right_files' => $rightFiles,
            ],
        ]);
    }

    public function runSelected(
        Request $request,
        SelectedFileReconciliationService $service
    ): JsonResponse {
        $validated = $request->validate([
            'batch_id' => ['required', 'exists:reconciliation_batches,id'],
            'matching_set_id' => ['required', 'exists:matching_sets,id'],
            'left_file_id' => ['required', 'exists:source_files,id'],
            'right_file_id' => ['required', 'exists:source_files,id'],
        ]);

        $summary = $service->run(
            ReconciliationBatch::findOrFail($validated['batch_id']),
            MatchingSet::findOrFail($validated['matching_set_id']),
            SourceFile::findOrFail($validated['left_file_id']),
            SourceFile::findOrFail($validated['right_file_id'])
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Selected file reconciliation completed successfully.',
            'data' => $summary,
        ]);
    }
}