<?php

use Illuminate\Support\Facades\Route;
use App\Modules\FileManager\Controllers\FileManagerController;

Route::get('/file-manager-page', [FileManagerController::class, 'fileManagerPage'])->name('fileManager.page');
Route::get('/get-files', [FileManagerController::class, 'getFilesByGroup'])->name('fileManager.getFiles');
Route::get('/file-manager/get-directory-tree', [FileManagerController::class, 'getDirectoryTree'])->name('fileManager.getDirectoryTree');

Route::get('/file-manager/get-directories', [FileManagerController::class, 'getDirectories'])->name('fileManager.getDirectories');
Route::post('/file-manager/create-folder', [FileManagerController::class, 'createFolder'])->name('fileManager.createFolder');
Route::post('/file-manager/update-path', [FileManagerController::class, 'updateFilePath'])->name('fileManager.updatePath');
Route::delete('/file-manager/delete-folder', [FileManagerController::class, 'deleteFolder'])->name('fileManager.deleteFolder');

Route::get('/file-manager/images', [FileManagerController::class, 'getAllImages'])->name('fileManager.getImages');
