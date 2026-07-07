<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BatchFile;
use App\Models\ExceptionRecord;
use App\Models\ReconciliationBatch;
use App\Models\ReconciliationResult;
use App\Models\ReconciliationResultFile;
use App\Models\SourceFile;
use Illuminate\Http\JsonResponse;

class MisDashboardController extends Controller
{
    public function summary(): JsonResponse
    {
        return response()->json([
            'status' => 'success',

            'data' => [

                /*
                |--------------------------------------------------------------------------
                | Upload Repository
                |--------------------------------------------------------------------------
                */

                'source_files' => [
                    'total' => SourceFile::count(),
                    'received' => SourceFile::where('status', 'RECEIVED')->count(),
                    'staged' => SourceFile::where('status', 'STAGED')->count(),
                    'completed' => SourceFile::where('processing_status', 'COMPLETED')->count(),
                ],

                /*
                |--------------------------------------------------------------------------
                | Batch Processing
                |--------------------------------------------------------------------------
                */

                'batches' => [
                    'total' => ReconciliationBatch::count(),
                    'completed' => ReconciliationBatch::where('status', 'COMPLETED')->count(),
                    'running' => ReconciliationBatch::where('status', 'RECONCILING')->count(),
                    'failed' => ReconciliationBatch::where('status', 'FAILED')->count(),
                ],

                /*
                |--------------------------------------------------------------------------
                | Batch Files
                |--------------------------------------------------------------------------
                */

                'batch_files' => [
                    'selected' => BatchFile::count(),
                    'processed' => BatchFile::where('status', 'PROCESSED')->count(),
                ],

                /*
                |--------------------------------------------------------------------------
                | Reconciliation
                |--------------------------------------------------------------------------
                */

                'reconciliation' => [
                    'total_results' => ReconciliationResult::count(),
                    'matched' => ReconciliationResult::where('result_status', 'MATCHED')->count(),
                    'exceptions' => ReconciliationResult::where('result_status', 'EXCEPTION')->count(),
                ],

                /*
                |--------------------------------------------------------------------------
                | Exception Workbench
                |--------------------------------------------------------------------------
                */

                'exceptions' => [

                    'total' => ExceptionRecord::count(),

                    'open' =>
                        ExceptionRecord::where('status', 'OPEN')->count(),

                    'assigned' =>
                        ExceptionRecord::where('status', 'ASSIGNED')->count(),

                    'resolved' =>
                        ExceptionRecord::where('status', 'RESOLVED')->count(),

                    'verified' =>
                        ExceptionRecord::where('status', 'VERIFIED')->count(),

                    'closed' =>
                        ExceptionRecord::where('status', 'CLOSED')->count(),

                    'sla_breached' =>
                        ExceptionRecord::where('sla_breached', true)->count(),
                ],

                /*
                |--------------------------------------------------------------------------
                | Result Repository
                |--------------------------------------------------------------------------
                */

                'result_files' => [

                    'generated' =>
                        ReconciliationResultFile::count(),

                    'ready' =>
                        ReconciliationResultFile::where('status', 'READY')->count(),

                    'failed' =>
                        ReconciliationResultFile::where('status', 'FAILED')->count(),
                ],
            ],
        ]);
    }

    public function analytics()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'reconciliation_by_status' => \App\Models\ReconciliationResult::selectRaw('result_status, COUNT(*) as total')
                    ->groupBy('result_status')
                    ->get(),

                'exceptions_by_code' => \App\Models\ExceptionRecord::selectRaw('exception_code, COUNT(*) as total')
                    ->groupBy('exception_code')
                    ->get(),

                'exceptions_by_priority' => \App\Models\ExceptionRecord::selectRaw('priority, COUNT(*) as total')
                    ->groupBy('priority')
                    ->get(),

                'batches_by_status' => \App\Models\ReconciliationBatch::selectRaw('status, COUNT(*) as total')
                    ->groupBy('status')
                    ->get(),

                'result_files_by_matching_set' => \App\Models\ReconciliationResultFile::selectRaw('matching_set_id, COUNT(*) as total')
                    ->groupBy('matching_set_id')
                    ->get(),
            ],
        ]);
    }
}