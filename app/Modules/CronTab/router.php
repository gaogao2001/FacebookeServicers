<?php

use Illuminate\Support\Facades\Route;
use App\Modules\CronTab\Controllers\CrontabController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;

Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {
    //social account
    Route::get('/crontab-page', [CrontabController::class, 'crontabPage'])->name('crontab-page');
});

Route::post('/crontab-submit', [CrontabController::class, 'submitCronTab'])->name('crontab-submit');
Route::get('/crontab-path', [CrontabController::class, 'getPaths'])->name('crontab-path');
Route::get('/crontab-show/{id}', [CrontabController::class, 'show'])->name('crontab-show');
Route::put('/crontab-update/{index}', [CrontabController::class, 'updateCronTab'])->name('crontab-update');
Route::post('/crontab-delete', [CrontabController::class, 'deleteCronTab'])->name('crontab-delete');
Route::post('/crontab-delete-all', [CrontabController::class, 'deleteAllCronTab'])->name('crontab-delete-all');
