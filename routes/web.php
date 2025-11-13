<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\StatsController;

Route::prefix('api')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);

    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update']);
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']);

    Route::get('/stats/totals-by-category', [StatsController::class, 'totalsByCategory']);
});

// Catch-all route: return the SPA view for any route that doesn't match API routes
// This allows Vue Router to handle client-side routing
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
