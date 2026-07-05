<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReconciliationResult;
use Illuminate\Http\Request;

class TransactionSearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'txn_id'            => 'nullable|string',
            'transaction_id'    => 'nullable|string',
            'consumer_number'   => 'nullable|string',
            'settlement_ref'    => 'nullable|string',
            'utr_number'        => 'nullable|string',
            'status'            => 'nullable|string',
            'result_status'     => 'nullable|string',
            'exception_code'    => 'nullable|string',
            'business_date'     => 'nullable|date',
            'batch_id'          => 'nullable|integer',
            'matching_set_id'   => 'nullable|integer',
        ]);

        if (
            !$request->filled('txn_id') &&
            !$request->filled('transaction_id') &&
            !$request->filled('consumer_number') &&
            !$request->filled('settlement_ref') &&
            !$request->filled('utr_number') &&
            !$request->filled('status') &&
            !$request->filled('result_status') &&
            !$request->filled('exception_code') &&
            !$request->filled('business_date') &&
            !$request->filled('batch_id') &&
            !$request->filled('matching_set_id')
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide at least one search parameter.',
            ], 422);
        }

        $query = ReconciliationResult::query()
            ->with([
                'batch',
                'matchingSet',
                'leftSource',
                'rightSource',
            ])
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | Transaction Id
        |--------------------------------------------------------------------------
        */

        if ($request->filled('txn_id')) {
            $query->where('transaction_id', $request->txn_id);
        }

        if ($request->filled('transaction_id')) {
            $query->where('transaction_id', $request->transaction_id);
        }

        /*
        |--------------------------------------------------------------------------
        | Consumer
        |--------------------------------------------------------------------------
        */

        if ($request->filled('consumer_number')) {
            $query->where('consumer_number', $request->consumer_number);
        }

        /*
        |--------------------------------------------------------------------------
        | Settlement
        |--------------------------------------------------------------------------
        */

        if ($request->filled('settlement_ref')) {
            $query->where('settlement_ref', $request->settlement_ref);
        }

        if ($request->filled('utr_number')) {
            $query->where('utr_number', $request->utr_number);
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where('result_status', strtoupper($request->status));
        }

        if ($request->filled('result_status')) {
            $query->where('result_status', strtoupper($request->result_status));
        }

        /*
        |--------------------------------------------------------------------------
        | Exception
        |--------------------------------------------------------------------------
        */

        if ($request->filled('exception_code')) {
            $query->where('exception_code', strtoupper($request->exception_code));
        }

        /*
        |--------------------------------------------------------------------------
        | Batch
        |--------------------------------------------------------------------------
        */

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('matching_set_id')) {
            $query->where('matching_set_id', $request->matching_set_id);
        }

        /*
        |--------------------------------------------------------------------------
        | Business Date
        |--------------------------------------------------------------------------
        */

        if ($request->filled('business_date')) {
            $query->whereDate('business_date', $request->business_date);
        }

        $results = $query->paginate(50);

        return response()->json([
            'status' => 'success',
            'count' => $results->total(),
            'data' => $results,
        ]);
    }
}