<?php

use Illuminate\Support\Facades\Route;
use App\InterfaceModules\DeviceEmulator\Mobile\Controllers\MobileController;

/* Khu vực router dành cho Mobile */
Route::any('/Mobile/Unlock/{uid?}', [MobileController::class, 'unlock'])
    ->where('uid', '.*')
    ->name('mobile.unlock');