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
            ->where('reconciliation_status', 'NOT_USED')
            ->where('is_locked', false)
            ->whereNotNull('business_date')
            ->whereIn('status', ['RECEIVED', 'STAGED'])
            ->whereIn('processing_status', ['NOT_STARTED', 'STAGED', 'COMPLETED'])
            ->orderByDesc('business_date')
            ->orderByDesc('created_at')
            ->get();

        $rightFiles = SourceFile::with('source')
            ->whereHas('source', function ($query) use ($matchingSet) {
                $query->where('source_type', $matchingSet->right_source_type);
            })
            ->where('reconciliation_status', 'NOT_USED')
            ->where('is_locked', false)
            ->whereNotNull('business_date')
            ->whereIn('status', ['RECEIVED', 'STAGED'])
            ->whereIn('processing_status', ['NOT_STARTED', 'STAGED', 'COMPLETED'])
            ->orderByDesc('business_date')
            ->orderByDesc('created_at')
            ->get();

        $availableDates = $leftFiles
            ->pluck('business_date')
            ->merge($rightFiles->pluck('business_date'))
            ->filter()
            ->map(fn ($date) => \Carbon\Carbon::parse($date)->format('Y-m-d'))
            ->unique()
            ->sortDesc()
            ->values();

        $recommendedPair = null;

        foreach ($availableDates as $date) {
            $leftFile = $leftFiles->first(function ($file) use ($date) {
                return $file->business_date &&
                    \Carbon\Carbon::parse($file->business_date)->format('Y-m-d') === $date;
            });

            $rightFile = $rightFiles->first(function ($file) use ($date) {
                return $file->business_date &&
                    \Carbon\Carbon::parse($file->business_date)->format('Y-m-d') === $date;
            });

            if ($leftFile && $rightFile) {
                $recommendedPair = [
                    'business_date' => $date,
                    'left_file_id' => $leftFile->id,
                    'left_file_name' => $leftFile->file_name,
                    'right_file_id' => $rightFile->id,
                    'right_file_name' => $rightFile->file_name,
                ];

                break;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'matching_set' => $matchingSet,
                'available_dates' => $availableDates,
                'recommended_pair' => $recommendedPair,
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

    public function history(): JsonResponse
    {
        $batches = ReconciliationBatch::with([
            'batchFiles.sourceFile.source',
        ])
            ->latest()
            ->paginate(50);

        return response()->json([
            'status' => 'success',
            'count' => $batches->total(),
            'data' => $batches,
        ]);
    }
}

AuditLogService::log(
    user: auth()->user(),
    module: 'RECONCILIATION',
    action: 'RUN_STARTED',
    description: 'Started reconciliation batch '.$batch->batch_code,
    request: request()
);

AuditLogService::log(
    user: auth()->user(),
    module: 'RECONCILIATION',
    action: 'RUN_COMPLETED',
    description: 'Completed reconciliation batch '.$batch->batch_code,
    request: request()
);

AuditLogService::log(
    user: auth()->user(),
    module: 'RECONCILIATION',
    action: 'RUN_FAILED',
    description: $exception->getMessage(),
    request: request()
);