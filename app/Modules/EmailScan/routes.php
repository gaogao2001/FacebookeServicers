<?php

use Illuminate\Support\Facades\Route;
use App\Modules\EmailScan\Controllers\EmailScanController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;


Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {

    Route::get('/email-scan-page', [EmailScanController::class, 'index']);
});
Route::get('/email-scan', [EmailScanController::class, 'emailScan']);
Route::post('/email-scan', [EmailScanController::class, 'addEmailScan']);
Route::get('/email-scan/{id}', [EmailScanController::class, 'showEmailScan']);
Route::put('/email-scan/{id}', [EmailScanController::class, 'updateEmailScan']);
Route::delete('/email-scan/{id}', [EmailScanController::class, 'delete']);
Route::get('/email-scan/search', [EmailScanController::class, 'searchEmailScan'])->name('email-scan.search');