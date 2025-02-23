<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Zalo\Controllers\ZaloController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;

Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {
    Route::get('/zalo_page', [ZaloController::class, 'zaloPage']);
});

Route::get('/zalos-edit/{id}', [ZaloController::class, 'show'])->name('zalos.edit');
Route::put('/zalos-update/{id}', [ZaloController::class, 'update'])->name('zalos.update');
