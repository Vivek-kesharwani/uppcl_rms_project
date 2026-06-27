<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionSearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'txn_id' => 'nullable|string',
            'account_no' => 'nullable|string',
            'bank_ref_no' => 'nullable|string',
            'discom' => 'nullable|string',
            'status' => 'nullable|string',
            'exception' => 'nullable|string',
        ]);

        if (
            !$request->txn_id &&
            !$request->account_no &&
            !$request->bank_ref_no &&
            !$request->discom &&
            !$request->status &&
            !$request->exception
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide at least one search parameter'
            ], 422);
        }

        $query = DB::table('reconciliation_masters')
            ->leftJoin('agency_transactions', 'reconciliation_masters.agency_transaction_id', '=', 'agency_transactions.id')
            ->leftJoin('billing_transactions', 'reconciliation_masters.billing_transaction_id', '=', 'billing_transactions.id')
            ->leftJoin('bank_settlements', 'reconciliation_masters.bank_settlement_id', '=', 'bank_settlements.id')
            ->leftJoin('exception_records', 'exception_records.reconciliation_master_id', '=', 'reconciliation_masters.id')
            ->select(
                'reconciliation_masters.id as recon_id',
                'reconciliation_masters.txn_id',
                'reconciliation_masters.account_no',
                'reconciliation_masters.discom',
                'reconciliation_masters.recon_status',
                'reconciliation_masters.exception_type',
                'reconciliation_masters.variance_amount',

                'agency_transactions.amount as agency_amount',
                'agency_transactions.txn_date as agency_date',
                'agency_transactions.txn_time as agency_time',

                'billing_transactions.amount as billing_amount',
                'billing_transactions.agency_name',

                'bank_settlements.bank_ref_no',
                'bank_settlements.settled_amount',
                'bank_settlements.settlement_status',
                'bank_settlements.payment_gateway',

                'exception_records.id as exception_id',
                'exception_records.exception_code',
                'exception_records.severity',
                'exception_records.status as exception_status',
                'exception_records.assigned_role',
                'exception_records.assigned_to',
                'exception_records.resolved_by',
                'exception_records.resolved_at'
            );

        if ($request->txn_id) {
            $query->where('reconciliation_masters.txn_id', $request->txn_id);
        }

        if ($request->account_no) {
            $query->where('reconciliation_masters.account_no', $request->account_no);
        }

        if ($request->bank_ref_no) {
            $query->where('bank_settlements.bank_ref_no', $request->bank_ref_no);
        }

        if ($request->discom) {
            $query->where('reconciliation_masters.discom', $request->discom);
        }

        if ($request->status) {
            $query->where('reconciliation_masters.recon_status', $request->status);
        }

        if ($request->exception) {
            $query->where('exception_records.exception_code', $request->exception);
        }

        $results = $query->orderBy('reconciliation_masters.id', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'count' => $results->count(),
            'data' => $results
        ]);
    }
}