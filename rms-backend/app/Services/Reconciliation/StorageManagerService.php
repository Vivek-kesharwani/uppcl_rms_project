<?php

namespace App\Services\Reconciliation;

use App\Models\Source;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class StorageManagerService
{
    public function store(
        UploadedFile $file,
        Source $source,
        array $parsed
    ): array {
        $folderPath = $parsed['file_type'] === 'DAILY'
            ? $source->daily_folder_path
            : $source->monthly_folder_path;

        $absoluteFolderPath = base_path($folderPath);

        if (!File::exists($absoluteFolderPath)) {
            File::makeDirectory($absoluteFolderPath, 0775, true);
        }

        $fileName = $parsed['original_file_name'];

        $absoluteFilePath = $absoluteFolderPath . DIRECTORY_SEPARATOR . $fileName;

        if (File::exists($absoluteFilePath)) {
            $timestamp = now()->format('YmdHis');
            $fileName = pathinfo($parsed['original_file_name'], PATHINFO_FILENAME)
                . "_{$timestamp}.csv";

            $absoluteFilePath = $absoluteFolderPath . DIRECTORY_SEPARATOR . $fileName;
        }

        $file->move($absoluteFolderPath, $fileName);

        return [
            'file_name' => $fileName,
            'file_path' => $absoluteFilePath,
            'relative_path' => $folderPath . DIRECTORY_SEPARATOR . $fileName,
            'file_size' => File::size($absoluteFilePath),
            'checksum' => hash_file('sha256', $absoluteFilePath),
        ];
    }
}