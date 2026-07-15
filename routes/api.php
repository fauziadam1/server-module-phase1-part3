<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/currencies', [CurrencyController::class, 'all']);
        Route::get('/categories', [CategoryController::class, 'all']);

        Route::get('/wallets/{id}', [WalletController::class, 'index']);
        Route::get('/wallets', [WalletController::class, 'all']);
        Route::post('/wallets', [WalletController::class, 'store']);
        Route::put('/wallets/{id}', [WalletController::class, 'update']);
        Route::delete('/wallets/{id}', [WalletController::class, 'delete']);

        Route::get('/transactions', [TransactionController::class, 'all']);
        Route::post('/transactions', [TransactionController::class, 'store']);
        Route::delete('/transactions/{id}', [TransactionController::class, 'delete']);

        Route::get('/wallets/{id}/reports/summary/expense', [ReportController::class, 'expense']);
        Route::get('/wallets/{id}/reports/summary/income', [ReportController::class, 'income']);
    });
});
