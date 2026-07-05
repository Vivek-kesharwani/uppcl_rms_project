<?php

namespace App\Services\Reconciliation;

use Carbon\Carbon;
use InvalidArgumentException;

class FilenameParserService
{
    public function parse(string $fileName): array
    {
        $fileName = basename($fileName);

        if (!preg_match('/^(.+)_(daily|monthly)_(\d{6}|\d{8})\.csv$/i', $fileName, $matches)) {
            throw new InvalidArgumentException(
                'Invalid file naming format. Expected: SourceName_daily_DDMMYYYY.csv or SourceName_monthly_MMYYYY.csv'
            );
        }

        $sourceName = $matches[1];
        $period = strtoupper($matches[2]);
        $datePart = $matches[3];

        if ($period === 'DAILY') {
            if (strlen($datePart) !== 8) {
                throw new InvalidArgumentException('Daily file must contain date in DDMMYYYY format.');
            }

            $date = Carbon::createFromFormat('dmY', $datePart);

            if (!$date || $date->format('dmY') !== $datePart) {
                throw new InvalidArgumentException('Invalid daily business date.');
            }

            return [
                'source_name' => $sourceName,
                'file_type' => 'DAILY',
                'business_date' => $date->format('Y-m-d'),
                'business_month' => null,
                'original_file_name' => $fileName,
            ];
        }

        if ($period === 'MONTHLY') {
            if (strlen($datePart) !== 6) {
                throw new InvalidArgumentException('Monthly file must contain month in MMYYYY format.');
            }

            $date = Carbon::createFromFormat('mY', $datePart);

            if (!$date || $date->format('mY') !== $datePart) {
                throw new InvalidArgumentException('Invalid monthly business month.');
            }

            return [
                'source_name' => $sourceName,
                'file_type' => 'MONTHLY',
                'business_date' => null,
                'business_month' => $datePart,
                'original_file_name' => $fileName,
            ];
        }

        throw new InvalidArgumentException('Unsupported file period.');
    }
}