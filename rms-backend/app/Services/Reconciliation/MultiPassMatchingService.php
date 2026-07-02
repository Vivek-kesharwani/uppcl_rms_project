<?php

namespace App\Services\Reconciliation;

use App\Models\StagingTransaction;
use Illuminate\Support\Collection;

class MultiPassMatchingService
{
    public function findMatch(
        StagingTransaction $leftRecord,
        Collection $rightRecords
    ): ?StagingTransaction {
        // Pass 1: Transaction ID
        $match = $rightRecords->firstWhere('transaction_id', $leftRecord->transaction_id);

        if ($match) {
            return $match;
        }

        // Pass 2: Consumer + Amount + Date
        $match = $rightRecords->first(function ($rightRecord) use ($leftRecord) {
            return $rightRecord->consumer_number === $leftRecord->consumer_number
                && number_format((float) $rightRecord->amount, 2, '.', '') === number_format((float) $leftRecord->amount, 2, '.', '')
                && optional($rightRecord->transaction_date)->format('Y-m-d') === optional($leftRecord->transaction_date)->format('Y-m-d');
        });

        if ($match) {
            return $match;
        }

        // Pass 3: Settlement reference
        if ($leftRecord->settlement_ref) {
            $match = $rightRecords->firstWhere('settlement_ref', $leftRecord->settlement_ref);

            if ($match) {
                return $match;
            }
        }

        // Pass 4: UTR number
        if ($leftRecord->utr_number) {
            $match = $rightRecords->firstWhere('utr_number', $leftRecord->utr_number);

            if ($match) {
                return $match;
            }
        }

        return null;
    }
}