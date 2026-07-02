<?php

namespace App\Services\Reconciliation;

use App\Models\SourceFile;
use Carbon\Carbon;

class FileRegistrationService
{
    public function register(array $detectedFile): ?SourceFile
    {
        $fileName = $detectedFile['file_name'];

        $periodInfo = $this->extractPeriodInfo($fileName, $detectedFile['file_type']);

        if (!$periodInfo) {
            return SourceFile::updateOrCreate(
                [
                    'source_id' => $detectedFile['source_id'],
                    'file_name' => $fileName,
                ],
                [
                    'file_path' => $detectedFile['file_path'],
                    'file_type' => $detectedFile['file_type'],
                    'file_size' => $detectedFile['file_size'],
                    'status' => 'FAILED',
                    'error_message' => 'Invalid file naming format',
                    'received_at' => now(),
                ]
            );
        }

        return SourceFile::updateOrCreate(
            [
                'source_id' => $detectedFile['source_id'],
                'file_name' => $fileName,
            ],
            
            [
                'file_path' => $detectedFile['file_path'],
                'file_type' => $detectedFile['file_type'],
                'business_date' => $periodInfo['business_date'],
                'business_month' => $periodInfo['business_month'],
                'file_size' => $detectedFile['file_size'],
                'status' => 'RECEIVED',
                'error_message' => null,
                'received_at' => now(),
            ]
        );

    }

    private function extractPeriodInfo(string $fileName, string $fileType): ?array
    {
        if ($fileType === 'DAILY') {
            preg_match('/daily_(\d{8})/i', $fileName, $matches);

            if (!$matches) {
                return null;
            }

            $date = Carbon::createFromFormat('dmY', $matches[1]);

            return [
                'business_date' => $date->format('Y-m-d'),
                'business_month' => null,
            ];
        }

        if ($fileType === 'MONTHLY') {
            preg_match('/monthly_(\d{6})/i', $fileName, $matches);

            if (!$matches) {
                return null;
            }

            return [
                'business_date' => null,
                'business_month' => $matches[1],
            ];
        }

        return null;
    }
}