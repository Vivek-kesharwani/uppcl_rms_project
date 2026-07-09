<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Daily Reconciliation Summary
     */
    public function dailyReconciliation()
    {
        $data = DB::table('reconciliation_results')
            ->select(
                'business_date',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw("SUM(CASE WHEN result_status = 'MATCHED' THEN 1 ELSE 0 END) as matched"),
                DB::raw("SUM(CASE WHEN result_status = 'EXCEPTION' THEN 1 ELSE 0 END) as exceptions")
            )
            ->groupBy('business_date')
            ->orderByDesc('business_date')
            ->get();

        return ApiResponse::success(
            $data,
            'Daily reconciliation report generated successfully.'
        );
    }

    /**
     * Exception Summary Report
     */
    public function exceptionSummary()
    {
        $data = DB::table('exception_records')
            ->select(
                'exception_code',
                'severity',
                'priority',
                'status',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(variance_amount) as total_variance')
            )
            ->groupBy(
                'exception_code',
                'severity',
                'priority',
                'status'
            )
            ->orderByDesc('total')
            ->get();

        return ApiResponse::success(
            $data,
            'Exception summary generated successfully.'
        );
    }

    /**
     * Settlement / Reconciliation Exception Summary
     *
     * Replaces the old bank_settlements report.
     */
    public function settlementSummary()
    {
        $data = DB::table('reconciliation_results')
            ->select(
                'exception_code',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(variance_amount) as total_variance')
            )
            ->whereNotNull('exception_code')
            ->groupBy('exception_code')
            ->orderByDesc('total_transactions')
            ->get();

        return ApiResponse::success(
            $data,
            'Settlement summary generated successfully.'
        );
    }
}