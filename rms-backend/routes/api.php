<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReconciliationController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExceptionController;
use App\Http\Controllers\Api\TransactionSearchController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\UploadHistoryController;

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

Route::post('/billing/upload', [UploadController::class, 'uploadBilling'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR']);

Route::post('/bank/upload', [UploadController::class, 'uploadBank'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR']);

Route::get('/dashboard/overview', [DashboardController::class, 'overview'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR,VIEWER']);

Route::get('/uploads', [UploadController::class, 'uploads'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR']);

Route::get('/exceptions', [ReconciliationController::class, 'exceptions'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR']);

Route::get('/exceptions/{id}', [ExceptionController::class, 'show'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR']);

Route::put('/exceptions/{id}', [ExceptionController::class, 'update'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR']);

Route::post('/exceptions/{id}/resolve', [ExceptionController::class, 'resolve'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR']);

Route::post('/exceptions/{id}/assign', [ExceptionController::class, 'assign'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN']);

Route::get('/dashboard/recent-uploads', [DashboardController::class, 'recentUploads'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR,VIEWER']);

Route::get('/dashboard/recent-exceptions', [DashboardController::class, 'recentExceptions'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR,VIEWER']);

Route::get('/dashboard/charts', [DashboardController::class, 'charts'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR,VIEWER']);

Route::get('/transactions/search', [TransactionSearchController::class, 'search'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR,VIEWER']);

Route::get('/reports/daily-reconciliation', [ReportController::class, 'dailyReconciliation'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR,VIEWER']);

Route::get('/reports/exception-summary', [ReportController::class, 'exceptionSummary'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR,VIEWER']);

Route::get('/reports/settlement-summary', [ReportController::class, 'settlementSummary'])
    ->middleware(['auth:sanctum', 'role:HQ_ADMIN,DISCOM_ADMIN,OPERATOR,VIEWER']);

Route::get('/upload-history', [UploadHistoryController::class, 'index']);