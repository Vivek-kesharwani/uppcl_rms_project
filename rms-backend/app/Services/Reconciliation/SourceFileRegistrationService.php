<?php

namespace App\Services\Reconciliation;

use App\Models\Source;
use App\Models\SourceFile;

class SourceFileRegistrationService
{
    public function register(
        Source $source,
        array $parsed,
        array $stored
    ): SourceFile {
        return SourceFile::updateOrCreate(
            [
                'source_id' => $source->id,
                'file_name' => $stored['file_name'],
            ],
            [
                'file_path' => $stored['file_path'],
                'file_type' => $parsed['file_type'],
                'business_date' => $parsed['business_date'],
                'business_month' => $parsed['business_month'],
                'file_size' => $stored['file_size'],
                'checksum' => $stored['checksum'],
                'status' => 'RECEIVED',
                'error_message' => null,
                'received_at' => now(),
            ]
        );
    }
}