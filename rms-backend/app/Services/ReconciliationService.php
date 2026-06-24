<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReconciliationService
{
    public function run(): array
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('exception_records')->truncate();
        DB::table('reconciliation_masters')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $agencyTransactions = DB::table('agency_transactions')->get();

        $summary = [
            'total' => 0,
            'matched' => 0,
            'exceptions' => 0,
            'amount_mismatch' => 0,
            'missing_settlement' => 0,
        ];

        foreach ($agencyTransactions as $agency) {
            $summary['total']++;

            $billing = DB::table('billing_transactions')
                ->where('txn_id', $agency->txn_id)
                ->first();

            $bank = DB::table('bank_settlements')
                ->where('txn_id', $agency->txn_id)
                ->first();

            $status = 'MATCHED';
            $exceptionType = null;
            $variance = 0;

            if (!$billing) {
                $status = 'EXCEPTION';
                $exceptionType = 'MISSING_BILLING';
                $summary['exceptions']++;
            } elseif (!$bank) {
                $status = 'EXCEPTION';
                $exceptionType = 'MISSING_SETTLEMENT';
                $summary['exceptions']++;
                $summary['missing_settlement']++;
            } elseif (
                $agency->amount != $billing->amount ||
                $agency->amount != $bank->settled_amount
            ) {
                $status = 'EXCEPTION';
                $exceptionType = 'AMOUNT_MISMATCH';
                $variance = abs($agency->amount - $billing->amount);
                $summary['exceptions']++;
                $summary['amount_mismatch']++;
            } else {
                $summary['matched']++;
            }

            $reconId = DB::table('reconciliation_masters')->insertGetId([
                'txn_id' => $agency->txn_id,
                'account_no' => $agency->account_no,
                'discom' => $agency->discom,
                'agency_transaction_id' => $agency->id,
                'billing_transaction_id' => $billing?->id,
                'bank_settlement_id' => $bank?->id,
                'recon_status' => $status,
                'exception_type' => $exceptionType,
                'variance_amount' => $variance,
                'last_evaluated' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($status === 'EXCEPTION') {
                DB::table('exception_records')->insert([
                    'reconciliation_master_id' => $reconId,
                    'txn_id' => $agency->txn_id,
                    'exception_code' => $exceptionType,
                    'severity' => $exceptionType === 'MISSING_SETTLEMENT' ? 'HIGH' : 'MEDIUM',
                    'variance_amount' => $variance,
                    'status' => 'OPEN',
                    'assigned_role' => $exceptionType === 'MISSING_SETTLEMENT' ? 'FINANCE_TEAM' : 'BILLING_TEAM',
                    'remarks' => $exceptionType,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return $summary;
    }
}