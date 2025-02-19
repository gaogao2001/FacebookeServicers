<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;

Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index']);
});
Route::get('/system_info', [DashboardController::class, 'show_system_info']);
Route::get('/user_page', [DashboardController::class, 'inforAccount']);
Route::get('/role_page', [DashboardController::class, 'show_role_page']);

Route::post('/reboot', [DashboardController::class, 'reboot'])->name('admin.reboot');
Route::post('/shutdown', [DashboardController::class, 'shutdown'])->name('admin.shutdown');
Route::post('/SeviceControler', [DashboardController::class, 'SeviceControler']);
Route::get('/system-info', [DashboardController::class, 'loadSystemInfo']);


///servicer controller
