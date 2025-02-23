<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Google\Controllers\GoogleController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;

Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {
    //social account
    Route::get('/google_page', [GoogleController::class, 'google_page']);

});

Route::get('/googles', [GoogleController::class, 'index']);
Route::post('/generate-google-code', [GoogleController::class, 'generateCode']);
Route::post('/googles', [GoogleController::class, 'store']);
Route::get('/googles/{id}', [GoogleController::class, 'show']);
Route::put('/googles/{id}', [GoogleController::class, 'update']);
Route::delete('/googles/{id}', [GoogleController::class, 'destroy']);
Route::post('/googles/delete-by-emails' , [GoogleController::class, 'deleteByEmails']);
Route::post('/googles/delete_all', [GoogleController::class, 'deleteAll'])->name('googles.delete_all');