<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReconciliationResult;
use Illuminate\Http\Request;

class ExceptionController extends Controller
{
    public function show($id)
    {
        $exception = ReconciliationResult::with([
            'batch',
            'matchingSet',
            'leftSource',
            'rightSource',
            'visibleToSource',
        ])
        ->where('result_status', 'EXCEPTION')
        ->find($id);

        if (!$exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $exception,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'nullable|string',
        ]);

        $exception = ReconciliationResult::where('result_status', 'EXCEPTION')
            ->find($id);

        if (!$exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception not found',
            ], 404);
        }

        $exception->update([
            'remarks' => $request->remarks,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exception updated successfully.',
            'data' => $exception,
        ]);
    }

    public function resolve(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string',
        ]);

        $exception = ReconciliationResult::where('result_status', 'EXCEPTION')
            ->find($id);

        if (!$exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception not found',
            ], 404);
        }

        $exception->update([
            'remarks' => $request->remarks,
            'result_status' => 'RESOLVED',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exception resolved successfully.',
            'data' => $exception,
        ]);
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'visible_to_source_id' => 'required|exists:sources,id',
        ]);

        $exception = ReconciliationResult::where('result_status', 'EXCEPTION')
            ->find($id);

        if (!$exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception not found',
            ], 404);
        }

        $exception->update([
            'visible_to_source_id' => $request->visible_to_source_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exception assigned successfully.',
            'data' => $exception,
        ]);
    }

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'count' => ReconciliationResult::where('result_status', 'EXCEPTION')->count(),
            'data' => ReconciliationResult::with([
                'batch',
                'matchingSet',
                'leftSource',
                'rightSource',
                'visibleToSource',
            ])
            ->where('result_status', 'EXCEPTION')
            ->latest()
            ->paginate(50),
        ]);
    }
}