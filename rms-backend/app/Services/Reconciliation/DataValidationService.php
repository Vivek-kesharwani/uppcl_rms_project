<?php

namespace App\Services\Reconciliation;

class DataValidationService
{
    public function validate(array $row): array
    {
        $errors = [];

        /*
        |--------------------------------------------------------------------------
        | Mandatory Fields
        |--------------------------------------------------------------------------
        */

        if (empty($row['transaction_id'])) {
            $errors[] = 'Transaction ID is mandatory.';
        }

        if (empty($row['consumer_number'])) {
            $errors[] = 'Consumer Number is mandatory.';
        }

        if (empty($row['amount'])) {
            $errors[] = 'Amount is mandatory.';
        }

        if (empty($row['transaction_date'])) {
            $errors[] = 'Transaction Date is mandatory.';
        }

        /*
        |--------------------------------------------------------------------------
        | Amount Validation
        |--------------------------------------------------------------------------
        */

        if (
            isset($row['amount']) &&
            (!is_numeric($row['amount']) || $row['amount'] < 0)
        ) {
            $errors[] = 'Invalid amount.';
        }

        /*
        |--------------------------------------------------------------------------
        | Date Validation
        |--------------------------------------------------------------------------
        */

        if (
            !empty($row['transaction_date']) &&
            !strtotime($row['transaction_date'])
        ) {
            $errors[] = 'Invalid transaction date.';
        }

        /*
        |--------------------------------------------------------------------------
        | Status Validation
        |--------------------------------------------------------------------------
        */

        $allowedStatus = [
            'SUCCESS',
            'FAILED',
            'PENDING',
        ];

        if (
            !empty($row['transaction_status']) &&
            !in_array($row['transaction_status'], $allowedStatus)
        ) {
            $errors[] = 'Invalid transaction status.';
        }

        return [
            'is_valid' => count($errors) === 0,
            'errors' => $errors,
            'clean_row' => $row,
        ];
    }
}