<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Reconciliation\DynamicFileUploadService;
use App\Models\SourceFile;

class UploadController extends Controller
{
    public function upload(
        Request $request,
        DynamicFileUploadService $uploadService
    ) {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        return response()->json(
            $uploadService->upload(
                $request->file('file')
            )
        );
    }

    public function uploads()
    {
        return response()->json([
            'status' => 'success',
            'data' => SourceFile::with('source')
                ->latest()
                ->get()
        ]);
    }
}

AuditLogService::log(
    user: auth()->user(),
    module: 'FILE_UPLOAD',
    action: 'UPLOAD',
    description: 'Uploaded file: '.$file->getClientOriginalName(),
    request: request()
);