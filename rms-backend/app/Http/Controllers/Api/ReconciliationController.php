<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReconciliationBatch;
use App\Services\Reconciliation\BatchExecutionService;
use Illuminate\Http\JsonResponse;

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
}