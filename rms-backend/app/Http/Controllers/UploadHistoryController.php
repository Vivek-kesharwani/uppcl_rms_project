<?php

namespace App\Http\Controllers;

use App\Models\SourceFile;
use Illuminate\Http\JsonResponse;

class UploadHistoryController extends Controller
{
    public function index(): JsonResponse
    {
        $files = SourceFile::with('source')
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'count' => $files->total(),
            'data' => $files,
        ]);
    }
}