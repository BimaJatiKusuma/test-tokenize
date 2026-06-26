<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LicenseController;

Route::post('/activate', [LicenseController::class, 'activate']);
