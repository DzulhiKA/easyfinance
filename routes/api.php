<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;

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
});
