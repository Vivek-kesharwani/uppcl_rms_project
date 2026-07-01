<?php

namespace App\Services\Reconciliation;

use App\Models\SourceFile;
use Illuminate\Support\Facades\File;

class FileValidationService
{
    public function validate(SourceFile $sourceFile): bool
    {
        $sourceFile->update([
            'status' => 'VALIDATING',
            'processing_started_at' => now(),
            'error_message' => null,
        ]);

        if (!File::exists($sourceFile->file_path)) {
            return $this->fail($sourceFile, 'File does not exist.');
        }

        if ($sourceFile->file_size <= 0) {
            return $this->fail($sourceFile, 'File is empty.');
        }

        if (strtolower(pathinfo($sourceFile->file_name, PATHINFO_EXTENSION)) !== 'csv') {
            return $this->fail($sourceFile, 'Only CSV files are supported currently.');
        }

        $sourceFile->update([
            'status' => 'CLEANING',
        ]);

        return true;
    }

    private function fail(SourceFile $sourceFile, string $message): bool
    {
        $sourceFile->update([
            'status' => 'FAILED',
            'error_message' => $message,
            'processing_completed_at' => now(),
        ]);

        return false;
    }
}