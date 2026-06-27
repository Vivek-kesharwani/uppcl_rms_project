<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReconciliationController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AuthController;

Route::post('/reconciliation/run', [ReconciliationController::class, 'run']);
Route::get('/reconciliation/summary', [ReconciliationController::class, 'summary']);
Route::get('/exceptions', [ReconciliationController::class, 'exceptions']);

Route::post('/agency/upload', [UploadController::class, 'uploadAgency'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR']);

Route::post('/billing/upload', [UploadController::class, 'uploadBilling']);
Route::post('/bank/upload', [UploadController::class, 'uploadBank']);

Route::get('/uploads', [UploadController::class, 'uploads']);
Route::get('/dashboard/overview', [DashboardController::class, 'overview']);

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
