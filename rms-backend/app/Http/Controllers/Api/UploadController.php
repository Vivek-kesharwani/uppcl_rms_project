<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CsvImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ReconciliationService;

class UploadController extends Controller
{
    public function uploadAgency(Request $request, CsvImportService $service, ReconciliationService $reconService)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads');

        $result = $service->importAgency($path);

        $this->logUpload('AGENCY', $file->getClientOriginalName(), $path, $result);
        
        if (($result['status'] ?? null) === 'success') {
            $result['reconciliation'] = $reconService->run();
        }

        return response()->json($result);
    }

    public function uploadBilling(Request $request, CsvImportService $service, ReconciliationService $reconService)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads');

        $result = $service->importBilling($path);

        $this->logUpload('BILLING', $file->getClientOriginalName(), $path, $result);

        if (($result['status'] ?? null) === 'success') {
            $result['reconciliation'] = $reconService->run();
        }

        return response()->json($result);
    }

    public function uploadBank(Request $request, CsvImportService $service, ReconciliationService $reconService)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads');

        $result = $service->importBank($path);

        $this->logUpload('BANK', $file->getClientOriginalName(), $path, $result);

        if (($result['status'] ?? null) === 'success') {
            $result['reconciliation'] = $reconService->run();
        }

        return response()->json($result);
    }

    public function uploads()
    {
        $uploads = DB::table('upload_logs')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $uploads
        ]);
    }

    private function logUpload(string $sourceType, string $fileName, string $filePath, array $result): void
    {
        DB::table('upload_logs')->insert([
            'source_type' => $sourceType,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'total_rows' => $result['inserted_rows'] ?? 0,
            'processed_rows' => $result['inserted_rows'] ?? 0,
            'status' => $result['status'] ?? 'UNKNOWN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}