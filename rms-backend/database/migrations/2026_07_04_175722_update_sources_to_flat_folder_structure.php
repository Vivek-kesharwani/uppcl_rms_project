<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $sources = DB::table('sources')->get();

        foreach ($sources as $source) {
            $folderName = $source->source_name;

            DB::table('sources')
                ->where('id', $source->id)
                ->update([
                    'daily_folder_path' => "storage/rms/inbound/{$folderName}/daily",
                    'monthly_folder_path' => "storage/rms/inbound/{$folderName}/monthly",
                    'daily_file_pattern' => "{$folderName}_daily_DDMMYYYY.csv",
                    'monthly_file_pattern' => "{$folderName}_monthly_MMYYYY.csv",
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // No rollback needed for folder normalization.
    }
};