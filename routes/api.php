<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExpenseController;

Route::apiResource('expenses', ExpenseController::class);
Route::get('expenses/dashboard/stats', [ExpenseController::class, 'dashboard']);

