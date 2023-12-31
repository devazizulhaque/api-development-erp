<?php

use App\Http\Controllers\AdminAllowanceTypeController;
use App\Http\Controllers\AdminCurrencyHistoryController;
use App\Http\Controllers\AdminDesignationController;
use App\Http\Controllers\AdminEmployeeController;
use App\Http\Controllers\AdminLeaveTypeController;
use App\Http\Controllers\AdminLoanTypeController;
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

Route::middleware(['check.token'])->group(function () {
    //admin designation
    Route::apiResource('/admin-designation', AdminDesignationController::class);
    Route::get('/admin-designation-overview', [AdminDesignationController::class, 'overview'])->name('admin-designation.overview');

    //admin leave type
    Route::apiResource('/admin-leave-type', AdminLeaveTypeController::class);
    Route::get('/admin-leave-type-overview', [AdminLeaveTypeController::class, 'overview'])->name('admin-leave.overview');

    //admin allowance type
    Route::apiResource('/admin-allowance-type', AdminAllowanceTypeController::class);
    Route::get('/admin-allowance-type-overview', [AdminAllowanceTypeController::class, 'overview'])->name('admin-allowance.overview');

    //admin loan type
    Route::apiResource('/admin-loan-type', AdminLoanTypeController::class);
    Route::get('/admin-loan-type-overview', [AdminLoanTypeController::class, 'overview'])->name('admin-loan.overview');
});


