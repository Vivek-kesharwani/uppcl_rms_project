<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dailyReconciliation()
    {
        return response()->json([
            'status' => 'success',
            'data' => DB::table('reconciliation_masters')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw("SUM(CASE WHEN recon_status = 'MATCHED' THEN 1 ELSE 0 END) as matched"),
                    DB::raw("SUM(CASE WHEN recon_status = 'EXCEPTION' THEN 1 ELSE 0 END) as exceptions")
                )
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date', 'desc')
                ->get()
        ]);
    }

    public function exceptionSummary()
    {
        return response()->json([
            'status' => 'success',
            'data' => DB::table('exception_records')
                ->select(
                    'exception_code',
                    'severity',
                    'status',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(variance_amount) as total_variance')
                )
                ->groupBy('exception_code', 'severity', 'status')
                ->get()
        ]);
    }

    public function settlementSummary()
    {
        return response()->json([
            'status' => 'success',
            'data' => DB::table('bank_settlements')
                ->select(
                    'payment_gateway',
                    'settlement_status',
                    DB::raw('COUNT(*) as total_settlements'),
                    DB::raw('SUM(settled_amount) as total_amount')
                )
                ->groupBy('payment_gateway', 'settlement_status')
                ->get()
        ]);
    }
}