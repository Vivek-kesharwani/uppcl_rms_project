<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReconciliationService;
use Illuminate\Support\Facades\DB;

class ReconciliationController extends Controller
{
    public function run(ReconciliationService $service)
    {
        $summary = $service->run();

        return response()->json([
            'status' => 'success',
            'message' => 'Reconciliation completed successfully',
            'data' => $summary
        ]);
    }

    public function summary()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_transactions' => DB::table('reconciliation_masters')->count(),
                'matched' => DB::table('reconciliation_masters')->where('recon_status', 'MATCHED')->count(),
                'exceptions' => DB::table('reconciliation_masters')->where('recon_status', 'EXCEPTION')->count(),
                'amount_mismatch' => DB::table('exception_records')->where('exception_code', 'AMOUNT_MISMATCH')->count(),
                'missing_settlement' => DB::table('exception_records')->where('exception_code', 'MISSING_SETTLEMENT')->count(),
            ]
        ]);
    }

    public function exceptions()
    {
        $exceptions = DB::table('exception_records')
            ->join('reconciliation_masters', 'exception_records.reconciliation_master_id', '=', 'reconciliation_masters.id')
            ->select(
                'exception_records.id',
                'exception_records.txn_id',
                'exception_records.exception_code',
                'exception_records.severity',
                'exception_records.variance_amount',
                'exception_records.status',
                'exception_records.assigned_role',
                'reconciliation_masters.account_no',
                'reconciliation_masters.discom'
            )
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $exceptions
        ]);
    }
}