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
            'data' => MatchingSet::where('is_active', true)
                ->orderBy('execution_order')
                ->get(),
        ]);
    }

    public function filesForMatchingSet(MatchingSet $matchingSet): JsonResponse
    {
        $leftFiles = SourceFile::with('source')
            ->whereHas('source', function ($query) use ($matchingSet) {
                $query->where('source_type', $matchingSet->left_source_type);
            })
            ->orderByDesc('business_date')
            ->orderByDesc('created_at')
            ->get();

        $rightFiles = SourceFile::with('source')
            ->whereHas('source', function ($query) use ($matchingSet) {
                $query->where('source_type', $matchingSet->right_source_type);
            })
            ->orderByDesc('business_date')
            ->orderByDesc('created_at')
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
            'matching_set_id' => ['required', 'integer', 'exists:matching_sets,id'],
            'left_file_id' => ['required', 'integer', 'exists:source_files,id'],
            'right_file_id' => ['required', 'integer', 'exists:source_files,id'],
        ]);

        $matchingSet = MatchingSet::findOrFail($validated['matching_set_id']);
        $leftFile = SourceFile::with('source')->findOrFail($validated['left_file_id']);
        $rightFile = SourceFile::with('source')->findOrFail($validated['right_file_id']);

        $summary = $service->run(
            $matchingSet,
            $leftFile,
            $rightFile
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Selected file reconciliation completed successfully.',
            'data' => $summary,
        ]);
    }
}