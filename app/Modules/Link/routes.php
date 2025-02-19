<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Link\Controllers\LinkController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;

Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {

    Route::get('/link-page', [LinkController::class, 'index']);
});
Route::get('/links', [LinkController::class, 'getLinks'])->name('links.data');
Route::post('/links', [LinkController::class, 'addNewUrlFacebook']);
Route::get('/links/{id}', [LinkController::class, 'showLink']);
Route::put('/links/{id}', [LinkController::class, 'updateLink']);
Route::delete('/links/{id}', [LinkController::class, 'deleteLink']);