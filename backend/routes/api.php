<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LicenseController;

// Endpoint publik (untuk login)
Route::post('/login', [AuthController::class, 'login']);

// Endpoint privat (wajib menyertakan Bearer Token di header)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/activate', [LicenseController::class, 'activate']);
});