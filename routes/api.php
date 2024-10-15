<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::prefix("/auth")->group(function(){
    Route::post("/register", [AuthController::class, 'register']);
    Route::post("/login", [AuthController::class, 'login']);
    Route::delete("/logout", [AuthController::class, 'logout'])->middleware("auth:api");
});

Route::prefix("/wallet")->middleware("auth:api")->group(function(){
    Route::post("/credit", [WalletController::class, 'credit']);
    Route::post("/debit", [WalletController::class, 'debit']);
    Route::get("/balance", [WalletController::class, 'balance']);
    Route::get("/{transaction}", [WalletController::class, 'balance']);
});
