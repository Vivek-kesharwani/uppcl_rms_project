<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CsvImportService;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadAgency(Request $request, CsvImportService $service)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $path = $request->file('file')->store('uploads');

        $result = $service->importAgency($path);

        return response()->json($result);
    }

    public function uploadBilling(Request $request, CsvImportService $service)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $path = $request->file('file')->store('uploads');

        $result = $service->importBilling($path);

        return response()->json($result);
    }

    public function uploadBank(Request $request, CsvImportService $service)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $path = $request->file('file')->store('uploads');

        $result = $service->importBank($path);

        return response()->json($result);
    }
}