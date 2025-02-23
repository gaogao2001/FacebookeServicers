<?php

use Illuminate\Support\Facades\Route;
use App\Modules\BackupData\Controllers\BackupDataController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;

Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {
    Route::get('/backup-data', [BackupDataController::class, 'index'])->name('backup-data.index');
});
//backup data
Route::post('/backup-database', [BackupDataController::class, 'backupDatabase'])->name('backup-database');
Route::post('/restore-database', [BackupDataController::class, 'restoreDatabase'])->name('restore-database');
Route::post('/delete-backup', [BackupDataController::class, 'deleteBackup'])->name('delete-backup');
Route::get('/get-collections/{database}', [BackupDataController::class, 'getCollections'])->name('get-collections');
Route::post('/download-backup', [BackupDataController::class, 'downloadBackup']);
Route::post('/upload-backup', [BackupDataController::class, 'uploadBackup']);
Route::post('/delete-multiple-backup', [BackupDataController::class, 'deleteMultipleBackups']);
