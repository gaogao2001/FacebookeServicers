<?php

use Illuminate\Support\Facades\Route;
use App\Modules\SiteManager\Controllers\SiteManagerController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;

Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {
    Route::get('/site-manager', [SiteManagerController::class, 'index'])->name('site-manager.index');
});

Route::post('/site-manager', [SiteManagerController::class, 'createSiteManager'])->name('site-manager');
Route::get('/site-manager/{id}', [SiteManagerController::class, 'show'])->name('site-manager.show');
Route::put('/site-manager/{id}', [SiteManagerController::class, 'update'])->name('site-manager.update');
