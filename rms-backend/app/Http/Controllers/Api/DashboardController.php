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
}