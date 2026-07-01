<?php

namespace App\Services\Reconciliation;

use App\Models\Source;
use Illuminate\Support\Facades\File;

class FolderScannerService
{
    public function scan(): array
    {
        $detectedFiles = [];

        $sources = Source::where('is_active', true)->get();

        foreach ($sources as $source) {
            $detectedFiles = array_merge(
                $detectedFiles,
                $this->scanSourceFolders($source)
            );
        }

        return $detectedFiles;
    }

    private function scanSourceFolders(Source $source): array
    {
        $files = [];

        $folders = [
            'DAILY' => $source->daily_folder_path,
            'MONTHLY' => $source->monthly_folder_path,
        ];

        foreach ($folders as $fileType => $folderPath) {
            if (!$folderPath) {
                continue;
            }

            $absolutePath = base_path($folderPath);

            if (!File::exists($absolutePath)) {
                File::makeDirectory($absolutePath, 0755, true);
                continue;
            }

            foreach (File::files($absolutePath) as $file) {
                $files[] = [
                    'source_id' => $source->id,
                    'source_name' => $source->source_name,
                    'source_type' => $source->source_type,
                    'file_type' => $fileType,
                    'file_name' => $file->getFilename(),
                    'file_path' => $file->getPathname(),
                    'file_size' => $file->getSize(),
                ];
            }
        }

        return $files;
    }
}