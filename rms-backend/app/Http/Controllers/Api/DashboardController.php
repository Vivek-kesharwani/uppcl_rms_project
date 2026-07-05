<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReconciliationBatch;
use App\Models\ReconciliationResult;
use App\Models\SourceFile;
use Illuminate\Http\Request;
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
                'total_files' => SourceFile::count(),
                'available_files' => SourceFile::where('file_status', 'AVAILABLE')->count(),
                'reconciled_files' => SourceFile::where('reconciliation_status', 'RECONCILED')->count(),
                'total_batches' => ReconciliationBatch::count(),
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
            'data' => SourceFile::with('source')
                ->latest()
                ->get()
        ]);
    }

    public function batches()
    {
        return response()->json([
            'status' => 'success',
            'data' => ReconciliationBatch::with('batchFiles.sourceFile.source')
                ->latest()
                ->get()
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

                'file_status' => SourceFile::select('file_status', DB::raw('COUNT(*) as total'))
                    ->groupBy('file_status')
                    ->get(),

                'processing_status' => SourceFile::select('processing_status', DB::raw('COUNT(*) as total'))
                    ->groupBy('processing_status')
                    ->get(),

                'reconciliation_status' => SourceFile::select('reconciliation_status', DB::raw('COUNT(*) as total'))
                    ->groupBy('reconciliation_status')
                    ->get(),
            ]
        ]);
    }

    public function fileRepository(Request $request)
    {
        $query = SourceFile::with('source')->latest();

        if ($request->filled('source_id')) {
            $query->where('source_id', $request->source_id);
        }

        if ($request->filled('source_type')) {
            $query->whereHas('source', function ($q) use ($request) {
                $q->where('source_type', $request->source_type);
            });
        }

        if ($request->filled('file_type')) {
            $query->where('file_type', strtoupper($request->file_type));
        }

        if ($request->filled('business_date')) {
            $query->whereDate('business_date', $request->business_date);
        }

        if ($request->filled('business_month')) {
            $query->where('business_month', $request->business_month);
        }

        if ($request->filled('file_status')) {
            $query->where('file_status', strtoupper($request->file_status));
        }

        if ($request->filled('processing_status')) {
            $query->where('processing_status', strtoupper($request->processing_status));
        }

        if ($request->filled('reconciliation_status')) {
            $query->where('reconciliation_status', strtoupper($request->reconciliation_status));
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->paginate(20),
        ]);
    }
}