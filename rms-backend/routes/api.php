<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReconciliationController;
use App\Http\Controllers\Api\UploadController;

Route::post('/reconciliation/run', [ReconciliationController::class, 'run']);
Route::get('/reconciliation/summary', [ReconciliationController::class, 'summary']);
Route::get('/exceptions', [ReconciliationController::class, 'exceptions']);

Route::post('/agency/upload', [UploadController::class, 'uploadAgency']);
Route::post('/billing/upload', [UploadController::class, 'uploadBilling']);
Route::post('/bank/upload', [UploadController::class, 'uploadBank']);