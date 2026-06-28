<?php

namespace App\Http\Controllers;

use App\Models\UploadLog;

class UploadHistoryController extends Controller
{
    public function index()
    {
        $uploads = UploadLog::latest()->get();

        return response()->json([
            'status' => 'success',
            'count' => $uploads->count(),
            'data' => $uploads,
        ]);
    }
}