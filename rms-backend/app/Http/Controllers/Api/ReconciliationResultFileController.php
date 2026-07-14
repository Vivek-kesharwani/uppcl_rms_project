<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReconciliationResultFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ReconciliationResultFileController extends Controller
{
    public function index(Request $request)
    {
        $query = ReconciliationResultFile::with([
            'batch',
            'matchingSet',
        ]);

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('matching_set_id')) {
            $query->where('matching_set_id', $request->matching_set_id);
        }

        if ($request->filled('business_date')) {
            $query->whereDate('business_date', $request->business_date);
        }

        if ($request->filled('business_month')) {
            $query->where('business_month', $request->business_month);
        }

        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->status));
        }

        $resultFiles = $query
            ->latest()
            ->paginate($request->integer('per_page', 50));

        return response()->json([
            'status' => 'success',
            'count' => $resultFiles->total(),
            'data' => $resultFiles,
        ]);
    }

    public function show($id)
    {
        $resultFile = ReconciliationResultFile::with([
            'batch',
            'matchingSet',
        ])->find($id);

        if (!$resultFile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Result file not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $resultFile,
        ]);
    }

    public function download($id)
    {
        $resultFile = ReconciliationResultFile::find($id);

        if (!$resultFile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Result file not found.',
            ], 404);
        }

        if (!File::exists($resultFile->file_path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Physical result file is missing from storage.',
            ], 404);
        }

        return response()->download(
            $resultFile->file_path,
            $resultFile->file_name
        );
    }
}

AuditLogService::log(
    user: auth()->user(),
    module: 'RESULT_FILE',
    action: 'VIEW',
    description: 'Viewed result file '.$result->file_name,
    request: request()
);

AuditLogService::log(
    user: auth()->user(),
    module: 'RESULT_FILE',
    action: 'DOWNLOAD',
    description: 'Downloaded result file '.$result->file_name,
    request: request()
);