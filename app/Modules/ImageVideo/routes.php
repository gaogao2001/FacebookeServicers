<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ImageVideo\Controllers\ImageVideoManagerController;
use App\Modules\ImageVideo\Controllers\VideoCreatorController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;


Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {
    Route::get('/video-creator-page', [VideoCreatorController::class, 'videoCreatorPage']);
});

Route::post('/get-video', [ImageVideoManagerController::class, 'postVideo']);
Route::get('/get-video/{id}', [ImageVideoManagerController::class, 'getVideo'])->name('getVideo');
Route::get('/get-image/{id}', [ImageVideoManagerController::class, 'getImage'])->name('getImage');
Route::put('/updateImage/{id}', [ImageVideoManagerController::class, 'updateImage'])->name('updateImage');
Route::post('/upload-image/{id}', [ImageVideoManagerController::class, 'uploadImage'])->name('uploadImage');
Route::post('/upload-video/{id}', [ImageVideoManagerController::class, 'uploadVideo'])->name('uploadVideo');
Route::delete('/delete-image/{id}', [ImageVideoManagerController::class, 'deleteImage'])->name('deleteImage');
Route::delete('/delete-video/{id}', [ImageVideoManagerController::class, 'deleteVideo'])->name('deleteVideo');
Route::post('/cut-video', [ImageVideoManagerController::class, 'cutVideo']);
Route::post('/get-video-by-url', [ImageVideoManagerController::class, 'getVideoByUrl']);



Route::post('/create-basic-video', [VideoCreatorController::class, 'createBasicVideo'])->name('createBasicVideo'); 
Route::post('/create-video-with-audio', [VideoCreatorController::class, 'createVideoWithAudio']);
Route::post('/extract-audio', [VideoCreatorController::class, 'extractAudio']);