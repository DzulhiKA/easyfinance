<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua route di sini otomatis punya prefix /api
| Contoh:
| - POST   /api/register
| - POST   /api/login
| - GET    /api/me
| - GET    /api/categories
| - GET    /api/transactions
/ - GET    /api/dashboard/summary
*/

// =======================
// AUTH (PUBLIC)
// =======================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// =======================
// AUTHENTICATED ROUTES (JWT)
// =======================
Route::middleware('auth:api')->group(function () {

    // Get authenticated user
    Route::get('/me', function (Request $request) {
        return response()->json(auth('api')->user());
    });

    // =======================
    // CATEGORY CRUD
    // =======================
    Route::apiResource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // =======================
    // TRANSACTION CRUD
    // =======================
    Route::apiResource('transactions', TransactionController::class)
        ->only(['index', 'store', 'destroy']);

    // =======================
    // DASHBOARD
    // =======================
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/chart', [DashboardController::class, 'chart']);

    // =======================
    // REPORTS
    // =======================
    Route::prefix('reports')->group(function () {
        Route::get('/monthly', [ReportController::class, 'monthly']);
        Route::get('/yearly', [ReportController::class, 'yearly']);
        Route::get('/category', [ReportController::class, 'category']);
    // PDF Export
        Route::get('/monthly/pdf', [ReportController::class, 'monthlyPdf']);
        Route::get('/yearly/pdf', [ReportController::class, 'yearlyPdf']);

    });
});
