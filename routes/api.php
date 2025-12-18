<?php

use App\Http\Controllers\Api\ApiQueueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Queue polling for authenticated customers
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/queue/my-position', [ApiQueueController::class, 'myPosition'])->name('api.queue.my-position');
});

// Public queue status (no auth required)
Route::get('/queue/status/{branchCode}', [ApiQueueController::class, 'branchQueueStatus'])->name('api.queue.branch-status');
