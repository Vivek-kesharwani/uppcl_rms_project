<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExceptionController;
use App\Http\Controllers\Api\ReconciliationController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TransactionSearchController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\UploadHistoryController;
use App\Http\Controllers\Api\ReconciliationResultFileController;
use App\Http\Controllers\Api\MisDashboardController;

Route::post('/login', [AuthController::class, 'login'])->name('login');

/*
|--------------------------------------------------------------------------
| Public Demo Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::get('/dashboard/overview', [DashboardController::class, 'overview']);
Route::get('/dashboard/files', [DashboardController::class, 'files']);
Route::get('/dashboard/batches', [DashboardController::class, 'batches']);
Route::get('/dashboard/results', [DashboardController::class, 'results']);
Route::get('/dashboard/exceptions', [DashboardController::class, 'exceptions']);
Route::get('/dashboard/charts', [DashboardController::class, 'charts']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/reconciliation/run/{batch}', [ReconciliationController::class, 'run']);

    Route::post('/upload', [UploadController::class, 'upload'])
        ->middleware('auth:sanctum');
    
    Route::get('/uploads', [UploadController::class, 'uploads'])
        ->middleware('auth:sanctum');

    Route::get('/uploads', [UploadController::class, 'uploads']);
    Route::get('/upload-history', [UploadHistoryController::class, 'index']);

    Route::get('/exceptions/{id}', [ExceptionController::class, 'show']);
    Route::put('/exceptions/{id}', [ExceptionController::class, 'update']);
    Route::post('/exceptions/{id}/resolve', [ExceptionController::class, 'resolve']);
    Route::post('/exceptions/{id}/assign', [ExceptionController::class, 'assign']);

    Route::get('/reports/daily-reconciliation', [ReportController::class, 'dailyReconciliation']);
    Route::get('/reports/exception-summary', [ReportController::class, 'exceptionSummary']);
    Route::get('/reports/settlement-summary', [ReportController::class, 'settlementSummary']);

    Route::get('/exceptions', [ExceptionController::class, 'index']);
    Route::get('/exceptions/{id}', [ExceptionController::class, 'show']);
    Route::put('/exceptions/{id}', [ExceptionController::class, 'update']);
    Route::post('/exceptions/{id}/assign', [ExceptionController::class, 'assign']);
    Route::post('/exceptions/{id}/resolve', [ExceptionController::class, 'resolve']);
    Route::post('/exceptions/{id}/verify', [ExceptionController::class, 'verify']);
    Route::post('/exceptions/{id}/close', [ExceptionController::class, 'close']);

    Route::post('/exceptions/{id}/reopen', [ExceptionController::class, 'reopen']);

    Route::get('/result-files', [ReconciliationResultFileController::class, 'index']);
    Route::get('/result-files/{id}', [ReconciliationResultFileController::class, 'show']);
    Route::get('/result-files/{id}/download', [ReconciliationResultFileController::class, 'download']);

    Route::get('/reconciliation/history', [ReconciliationController::class, 'history']);
});

Route::get('/reconciliation/matching-sets', [ReconciliationController::class, 'matchingSets']);
Route::get('/reconciliation/matching-sets/{matchingSet}/files', [ReconciliationController::class, 'filesForMatchingSet']);
Route::post('/reconciliation/run-selected', [ReconciliationController::class, 'runSelected']);

Route::get('/file-repository', [DashboardController::class, 'fileRepository']);
Route::get('/transactions/search', [TransactionSearchController::class, 'search']);
Route::get('/exceptions-summary', [ExceptionController::class, 'summary']);
Route::get('/mis/summary', [MisDashboardController::class, 'summary']);
Route::get('/mis/analytics', [MisDashboardController::class, 'analytics']);