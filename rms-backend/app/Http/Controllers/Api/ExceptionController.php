<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExceptionController extends Controller
{
    public function show($id)
    {
        $exception = DB::table('exception_records')
            ->join('reconciliation_masters', 'exception_records.reconciliation_master_id', '=', 'reconciliation_masters.id')
            ->select(
                'exception_records.*',
                'reconciliation_masters.account_no',
                'reconciliation_masters.discom',
                'reconciliation_masters.recon_status'
            )
            ->where('exception_records.id', $id)
            ->first();

        if (!$exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $exception
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:OPEN,IN_PROGRESS,RESOLVED,CLOSED',
            'remarks' => 'nullable|string'
        ]);

        $exception = DB::table('exception_records')->where('id', $id)->first();

        if (!$exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception not found'
            ], 404);
        }

        DB::table('exception_records')
            ->where('id', $id)
            ->update([
                'status' => $request->status,
                'remarks' => $request->remarks,
                'updated_at' => now(),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exception updated successfully'
        ]);
    }

    public function resolve(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string'
        ]);

        $exception = DB::table('exception_records')
            ->where('id', $id)
            ->first();

        if (!$exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception not found'
            ], 404);
        }

        DB::table('exception_records')
            ->where('id', $id)
            ->update([
                'status'      => 'RESOLVED',
                'remarks'     => $request->remarks,
                'resolved_by' => auth()->user()->name,
                'resolved_at' => now(),
                'updated_at'  => now(),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exception resolved successfully'
        ]);
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'assigned_role' => 'required|string',
            'assigned_to' => 'required|string',
        ]);

        $exception = DB::table('exception_records')
            ->where('id', $id)
            ->first();

        if (!$exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception not found'
            ], 404);
        }

        DB::table('exception_records')
            ->where('id', $id)
            ->update([
                'assigned_role' => $request->assigned_role,
                'assigned_to'   => $request->assigned_to,
                'assigned_at'   => now(),
                'status'        => 'IN_PROGRESS',
                'updated_at'    => now(),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exception assigned successfully'
        ]);
    }
}