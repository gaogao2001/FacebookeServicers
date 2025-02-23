<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Fanpage\Controllers\FanpageManagerController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;

Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {
    Route::get('/fanpage_manager_page', [FanpageManagerController::class, 'fanpageManagerPage']);
});
Route::get('/admin/facebook/fanpages/data', [FanpageManagerController::class, 'getFanpageManager'])->name('admin.facebook.fanpage-manager.data');
Route::delete('/admin/facebook/fanpages/{id}', [FanpageManagerController::class, 'delete']);

Route::get('/admin/facebook/fanpages/edit/{id}', [FanpageManagerController::class, 'editPage'])->name('fanpage-manager.edit');
Route::put('/admin/facebook/fanpages/update/{id}', [FanpageManagerController::class, 'updateFanpage'])->name('fanpage-manager.update');
Route::post('/fanpage_update_coordinates/{id}', [FanpageManagerController::class, 'updateCoordinates'])->name('fanpage.updateCoordinates');

Route::post('/admin/facebook/fanpages/select-delete', [FanpageManagerController::class, 'selectDelete']);
Route::post('/admin/facebook/fanpages/delete-all', [FanpageManagerController::class, 'deleteAllFanpages']); 

Route::post('/fanpage-manager/filter', [FanpageManagerController::class, 'filterFanpages'])->name('fanpage-manager.filter');
Route::post('/fanpage-manager/clear-filter', [FanpageManagerController::class, 'clearFilter'])->name('fanpage-manager.clearFilter');

