<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReconciliationBatch;
use App\Models\ReconciliationResult;
use App\Models\SourceFile;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function overview()
    {
        $batch = ReconciliationBatch::latest()->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'batch' => $batch,
                'total_files' => $batch?->total_files ?? 0,
                'ready_files' => $batch?->ready_files ?? 0,
                'total_records' => $batch?->total_records ?? 0,
                'matched_records' => $batch?->matched_records ?? 0,
                'exception_records' => $batch?->exception_records ?? 0,
                'batch_status' => $batch?->status ?? 'NO_BATCH',
            ]
        ]);
    }

    public function files()
    {
        return response()->json([
            'status' => 'success',
            'data' => SourceFile::orderBy('source_id')->get()
        ]);
    }

    public function batches()
    {
        return response()->json([
            'status' => 'success',
            'data' => ReconciliationBatch::latest()->get()
        ]);
    }

    public function results()
    {
        return response()->json([
            'status' => 'success',
            'data' => ReconciliationResult::latest()->get()
        ]);
    }

    public function exceptions()
    {
        return response()->json([
            'status' => 'success',
            'data' => ReconciliationResult::where('result_status', 'EXCEPTION')
                ->latest()
                ->get()
        ]);
    }

    public function charts()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'result_status' => ReconciliationResult::select('result_status', DB::raw('COUNT(*) as total'))
                    ->groupBy('result_status')
                    ->get(),

                'exception_types' => ReconciliationResult::whereNotNull('exception_code')
                    ->select('exception_code', DB::raw('COUNT(*) as total'))
                    ->groupBy('exception_code')
                    ->get(),

                'file_status' => SourceFile::select('status', DB::raw('COUNT(*) as total'))
                    ->groupBy('status')
                    ->get(),
            ]
        ]);
    }
}