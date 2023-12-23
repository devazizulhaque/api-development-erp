<?php

use App\Http\Controllers\AdminCurrencyHistoryController;
use App\Http\Controllers\AdminEmployeeController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/admin-employee', [AdminEmployeeController::class, 'index']);
Route::post('/admin-employee', [AdminEmployeeController::class, 'store']);
Route::get('/admin-employee/{id}', [AdminEmployeeController::class, 'show']);
Route::post('/admin-employee/{id}', [AdminEmployeeController::class, 'update']);
Route::get('/admin-employee-overview', [AdminEmployeeController::class, 'overview']);


Route::get('/admin-employee/overview', [AdminEmployeeController::class, 'overview']);
Route::post('/admin-employee/export', [AdminEmployeeController::class, 'export']);



//admin currency
Route::get('/admin-currency-history/overview', [AdminCurrencyHistoryController::class, 'overview']);
Route::post('/admin-currency-history/export', [AdminCurrencyHistoryController::class, 'export']);
Route::get('/admin-currency-history', [AdminCurrencyHistoryController::class, 'admin_currency_histories']);
Route::get('/admin-currency-history/{id}', [AdminCurrencyHistoryController::class, 'admin_currency_history']);
Route::post('/admin-currency-history-store', [AdminCurrencyHistoryController::class, 'admin_currency_history_store']);
Route::post('/admin-currency-history-update/{id}', [AdminCurrencyHistoryController::class, 'admin_currency_history_update']);
