<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReconciliationController;

Route::post('/reconciliation/run', [ReconciliationController::class, 'run']);
Route::get('/reconciliation/summary', [ReconciliationController::class, 'summary']);
Route::get('/exceptions', [ReconciliationController::class, 'exceptions']);