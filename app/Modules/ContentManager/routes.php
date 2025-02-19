<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ContentManager\Controllers\ContentManagerController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;

Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {
    Route::get('/content-manager-page', [ContentManagerController::class, 'contentManagerPage']);
});

Route::get('/content-manager', [ContentManagerController::class, 'index'])->name('content-manager.index');
Route::post('/content-manager', [ContentManagerController::class, 'store'])->name('content-manager.store');
Route::get('/content-manager/{id}', [ContentManagerController::class, 'show'])->name('content-manager.show');
Route::put('/content-manager/{id}', [ContentManagerController::class, 'update'])->name('content-manager.update');
Route::delete('/content-manager/{id}', [ContentManagerController::class, 'destroy'])->name('content-manager.destroy');
Route::post('/upload-image', [ContentManagerController::class, 'uploadImage'])->name('upload-image');
Route::post('/update-coordinates', [ContentManagerController::class, 'updateCoordinates'])->name('content.updateCoordinates');
