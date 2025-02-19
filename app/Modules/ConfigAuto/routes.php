<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ConfigAuto\Controllers\AutoConfigController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;

// DASHBOARD & SideBar
Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {
    Route::get('/auto_config_page', [AutoConfigController::class, 'autoConfigPage'])->name('config.page');
    Route::get('/facebook/action-limit-page', [AutoConfigController::class, 'interactLimit'])->name('facebook.action.limit.page');
});

Route::post('/facebook/config', [AutoConfigController::class, 'saveFacebookConfig'])->name('facebook.config.save');
Route::post('/zalo/config', [AutoConfigController::class, 'saveZaloConfig'])->name('zalo.config.save');
Route::post('/facebook/config/update/{id}', [AutoConfigController::class, 'updateConfigFacebookById'])->name('facebook.config.update');
Route::post('/fanpage/config', [AutoConfigController::class, 'saveFanpageConfig'])->name('fanpage.config.save');
Route::post('/fanpage/config/update/{id}', [AutoConfigController::class, 'updateConfigFanpageById'])->name('fanpage.config.update');
Route::post('/facebook/action-limit', [AutoConfigController::class, 'saveInteractLimit'])->name('facebook.action.limit.save');

