<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// RUTAS LIBRES DE AUTH
Route::post('/login', [AuthController::class, 'login']);

// RUTAS PROTEGIDAS DE AUTH
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});