<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LicenseController;

Route::get('/', [LicenseController::class, 'index'])->name('licenses.index');
Route::post('/licenses/generate', [LicenseController::class, 'generate'])->name('licenses.generate');
Route::post('/licenses/{id}/force-expire', [LicenseController::class, 'forceExpire'])->name('licenses.force-expire');
