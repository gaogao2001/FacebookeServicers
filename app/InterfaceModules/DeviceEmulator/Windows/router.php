<?php

use Illuminate\Support\Facades\Route;
use App\InterfaceModules\DeviceEmulator\Windows\Controllers\WindowsController;



Route::get('/Windows/LoadAdsAccout/{uid?}', [WindowsController::class, 'LoadAdsAccount'])
    ->where('uid', '.*')
    ->name('windows.loadads');




/* Khu vực router dành cho Windows */
Route::any('/Windows/Unlock/{uid?}', [WindowsController::class, 'unlock'])
    ->where('uid', '.*')
    ->name('windows.unlock');