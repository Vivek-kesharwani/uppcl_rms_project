<?php

namespace App\Services\Reconciliation;

use Generator;

class CsvReaderService
{
    public function read(string $filePath): Generator
    {
        if (!file_exists($filePath)) {
            throw new \Exception("CSV file not found: {$filePath}");
        }

        $handle = fopen($filePath, 'r');

        if (!$handle) {
            throw new \Exception("Unable to open CSV file: {$filePath}");
        }

        $headers = fgetcsv($handle, 0, ',', '"', '\\');

        if (!$headers) {
            fclose($handle);
            throw new \Exception("CSV header row missing.");
        }

        $headers = array_map(fn ($header) => trim($header), $headers);

        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if (count($row) !== count($headers)) {
                continue;
            }

            yield array_combine($headers, $row);
        }

        fclose($handle);
    }
}