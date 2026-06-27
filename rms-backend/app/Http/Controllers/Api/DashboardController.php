<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function overview()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_transactions' => DB::table('reconciliation_masters')->count(),
                'matched' => DB::table('reconciliation_masters')->where('recon_status', 'MATCHED')->count(),
                'exceptions' => DB::table('reconciliation_masters')->where('recon_status', 'EXCEPTION')->count(),
                'amount_mismatch' => DB::table('exception_records')->where('exception_code', 'AMOUNT_MISMATCH')->count(),
                'missing_settlement' => DB::table('exception_records')->where('exception_code', 'MISSING_SETTLEMENT')->count(),
                'upload_count' => DB::table('upload_logs')->count(),
                'latest_uploads' => DB::table('upload_logs')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
            ]
        ]);
    }

    public function recentUploads()
    {
        return response()->json([
            'status' => 'success',
            'data' => DB::table('upload_logs')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
        ]);
    }

    public function recentExceptions()
    {
        return response()->json([
            'status' => 'success',
            'data' => DB::table('exception_records')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
        ]);
    }

    public function charts()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'exception_status' => DB::table('exception_records')
                    ->select('status', DB::raw('COUNT(*) as total'))
                    ->groupBy('status')
                    ->get(),

                'exception_types' => DB::table('exception_records')
                    ->select('exception_code', DB::raw('COUNT(*) as total'))
                    ->groupBy('exception_code')
                    ->get(),

                'reconciliation_status' => DB::table('reconciliation_masters')
                    ->select('recon_status', DB::raw('COUNT(*) as total'))
                    ->groupBy('recon_status')
                    ->get(),
            ]
        ]);
    }
}