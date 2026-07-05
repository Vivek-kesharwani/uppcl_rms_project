<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LegacyCsvImportService
{
    public function importAgency(string $filePath): array
    {
        $fullPath = storage_path('app/private/' . $filePath);

        if (!file_exists($fullPath)) {
            return [
                'status' => 'error',
                'message' => 'File not found',
            ];
        }

        $file = fopen($fullPath, 'r');

        $headers = fgetcsv($file);

        $normalizedHeaders = array_map(function ($header) {
            return strtolower(str_replace(' ', '_', trim($header)));
        }, $headers);

        $headerMap = [
            'discom' => 'discom',
            'account_no' => 'account_no',
            'a/c_no' => 'account_no',
            'transaction_id' => 'txn_id',
            'txn_id' => 'txn_id',
            'transaction_date' => 'txn_date',
            'date' => 'txn_date',
            'transaction_time' => 'txn_time',
            'time' => 'txn_time',
            'amount' => 'amount',
        ];

        $mappedHeaders = [];

        foreach ($normalizedHeaders as $header) {
            if (!isset($headerMap[$header])) {
                fclose($file);

                return [
                    'status' => 'error',
                    'message' => 'Unknown CSV header',
                    'header' => $header,
                ];
            }

            $mappedHeaders[] = $headerMap[$header];
        }

        $expectedHeaders = ['discom', 'account_no', 'txn_id', 'txn_date', 'txn_time', 'amount'];

        if ($mappedHeaders !== $expectedHeaders) {
            fclose($file);

            return [
                'status' => 'error',
                'message' => 'Invalid CSV header order',
                'expected' => $expectedHeaders,
                'received' => $mappedHeaders,
            ];
        }

        $inserted = 0;

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 6) {
                continue;
            }

            $row = array_map('trim', $row);

            if (
                empty($row[0]) &&
                empty($row[1]) &&
                empty($row[2]) &&
                empty($row[3]) &&
                empty($row[4]) &&
                empty($row[5])
            ) 
            {
                continue;
            }

            if (empty($row[2]) || empty($row[5])) {
                continue;
            }

            $txnDate = date('Y-m-d', strtotime($row[3]));
            $txnTime = date('H:i:s', strtotime($row[4]));

            DB::table('agency_transactions')->updateOrInsert(
                ['txn_id' => $row[2]],
                [
                    'discom' => $row[0],
                    'account_no' => $row[1],
                    'txn_id' => $row[2],
                    'txn_date' => $txnDate,
                    'txn_time' => $txnTime,
                    'amount' => $row[5],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $inserted++;
        }
        
        fclose($file);

        return [
            'status' => 'success',
            'message' => 'Agency CSV imported successfully',
            'inserted_rows' => $inserted,
        ];
    }

    public function importBilling(string $filePath): array
    {
        $fullPath = storage_path('app/private/' . $filePath);

        if (!file_exists($fullPath)) {
            return [
                'status' => 'error',
                'message' => 'File not found',
            ];
        }

        $file = fopen($fullPath, 'r');
        $headers = fgetcsv($file);

        $normalizedHeaders = array_map(function ($header) {
            return strtolower(str_replace(' ', '_', trim($header)));
        }, $headers);

        $headerMap = [
            'discom' => 'discom',
            'account_no' => 'account_no',
            'a/c_no' => 'account_no',
            'transaction_id' => 'txn_id',
            'txn_id' => 'txn_id',
            'transaction_date' => 'txn_date',
            'date' => 'txn_date',
            'transaction_time' => 'txn_time',
            'time' => 'txn_time',
            'amount' => 'amount',
            'agency_name' => 'agency_name',
        ];

        $mappedHeaders = [];

        foreach ($normalizedHeaders as $header) {
            if (!isset($headerMap[$header])) {
                fclose($file);

                return [
                    'status' => 'error',
                    'message' => 'Unknown CSV header',
                    'header' => $header,
                ];
            }

            $mappedHeaders[] = $headerMap[$header];
        }

        $expectedHeaders = ['discom', 'account_no', 'txn_id', 'txn_date', 'txn_time', 'amount', 'agency_name'];

        if ($mappedHeaders !== $expectedHeaders) {
            fclose($file);

            return [
                'status' => 'error',
                'message' => 'Invalid CSV header order',
                'expected' => $expectedHeaders,
                'received' => $mappedHeaders,
            ];
        }

        $inserted = 0;

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 7) {
                continue;
            }

            $row = array_map('trim', $row);

            if (empty($row[2]) || empty($row[5])) {
                continue;
            }

            $txnDate = date('Y-m-d', strtotime($row[3]));
            $txnTime = date('H:i:s', strtotime($row[4]));

            DB::table('billing_transactions')->updateOrInsert(
                ['txn_id' => $row[2]],
                [
                    'discom' => $row[0],
                    'account_no' => $row[1],
                    'txn_id' => $row[2],
                    'txn_date' => $txnDate,
                    'txn_time' => $txnTime,
                    'amount' => $row[5],
                    'agency_name' => $row[6],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $inserted++;
        }

        fclose($file);

        return [
            'status' => 'success',
            'message' => 'Billing CSV imported successfully',
            'inserted_rows' => $inserted,
        ];
    }

    public function importBank(string $filePath): array
    {
        $fullPath = storage_path('app/private/' . $filePath);

        if (!file_exists($fullPath)) {
            return [
                'status' => 'error',
                'message' => 'File not found',
            ];
        }

        $file = fopen($fullPath, 'r');
        $headers = fgetcsv($file);

        $normalizedHeaders = array_map(function ($header) {
            return strtolower(str_replace(' ', '_', trim($header)));
        }, $headers);

        $headerMap = [
            'bank_ref_no' => 'bank_ref_no',
            'transaction_id' => 'txn_id',
            'txn_id' => 'txn_id',
            'settlement_date' => 'settlement_date',
            'settlement_time' => 'settlement_time',
            'settled_amount' => 'settled_amount',
            'settlement_status' => 'settlement_status',
            'payment_gateway' => 'payment_gateway',
        ];

        $mappedHeaders = [];

        foreach ($normalizedHeaders as $header) {
            if (!isset($headerMap[$header])) {
                fclose($file);

                return [
                    'status' => 'error',
                    'message' => 'Unknown CSV header',
                    'header' => $header,
                ];
            }

            $mappedHeaders[] = $headerMap[$header];
        }

        $expectedHeaders = [
            'bank_ref_no',
            'txn_id',
            'settlement_date',
            'settlement_time',
            'settled_amount',
            'settlement_status',
            'payment_gateway'
        ];

        if ($mappedHeaders !== $expectedHeaders) {
            fclose($file);

            return [
                'status' => 'error',
                'message' => 'Invalid CSV header order',
                'expected' => $expectedHeaders,
                'received' => $mappedHeaders,
            ];
        }

        $inserted = 0;

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 7) {
                continue;
            }

            $row = array_map('trim', $row);

            if (empty($row[0]) || empty($row[1]) || empty($row[4])) {
                continue;
            }

            $settlementDate = date('Y-m-d', strtotime($row[2]));
            $settlementTime = date('H:i:s', strtotime($row[3]));

            DB::table('bank_settlements')->updateOrInsert(
                ['bank_ref_no' => $row[0]],
                [
                    'bank_ref_no' => $row[0],
                    'txn_id' => $row[1],
                    'settlement_date' => $settlementDate,
                    'settlement_time' => $settlementTime,
                    'settled_amount' => $row[4],
                    'settlement_status' => $row[5],
                    'payment_gateway' => $row[6],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $inserted++;
        }

        fclose($file);

        return [
            'status' => 'success',
            'message' => 'Bank CSV imported successfully',
            'inserted_rows' => $inserted,
        ];
    }
}