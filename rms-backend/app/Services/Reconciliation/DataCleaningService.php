<?php

namespace App\Services\Reconciliation;

class DataCleaningService
{
    public function clean(array $row): array
    {
        return [
            'transaction_id' =>
                isset($row['transaction_id'])
                    ? strtoupper(trim($row['transaction_id']))
                    : null,

            'consumer_number' =>
                isset($row['consumer_number'])
                    ? strtoupper(trim($row['consumer_number']))
                    : null,

            'account_number' =>
                isset($row['account_number'])
                    ? strtoupper(trim($row['account_number']))
                    : null,

            'amount' =>
                isset($row['amount'])
                    ? number_format((float)$row['amount'], 2, '.', '')
                    : null,

            'transaction_date' =>
                $row['transaction_date'] ?? null,

            'transaction_time' =>
                $row['transaction_time'] ?? null,

            'transaction_status' =>
                isset($row['transaction_status'])
                    ? strtoupper(trim($row['transaction_status']))
                    : null,

            // Preserve original row for audit/debugging
            'raw_payload' => json_encode($row),

            'settlement_ref' => isset($row['settlement_ref'])
                ? strtoupper(trim($row['settlement_ref']))
                : null,

            'utr_number' => isset($row['utr_number'])
                ? strtoupper(trim($row['utr_number']))
                : null,

            'settlement_date' => $row['settlement_date'] ?? null,

            'settlement_amount' => isset($row['settlement_amount'])
                ? number_format((float)$row['settlement_amount'], 2, '.', '')
                : null,
        ];
    }
}