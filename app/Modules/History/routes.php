<?php

use Illuminate\Support\Facades\Route;
use App\Modules\History\Controllers\FacebookHistoryController;
use App\Modules\History\Controllers\ZaloHistoryController;
use App\Modules\History\Controllers\NetworkHistoryController;
use App\Modules\History\Controllers\RequestHistoryController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;
use GuzzleHttp\Psr7\Request;

Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {

    Route::get('/facebook_history_page', [FacebookHistoryController::class, 'facebookHistoryPage']);
    Route::get('/zalo_history_page', [ZaloHistoryController::class, 'zaloHistoryPage']);
    Route::get('/network_history_page', [NetworkHistoryController::class, 'networkHistoryPage']);
    Route::get('/request_history_page', [RequestHistoryController::class, 'requestHistoryPage']);
});
//facebook history
Route::get('/history', [FacebookHistoryController::class, 'index']);
Route::delete('/history/{id}', [FacebookHistoryController::class, 'delete']);
Route::post('/history/all-delete', [FacebookHistoryController::class, 'allDelete']);
Route::get('/facebook/history/{uid}', [FacebookHistoryController::class, 'getHistoryByUid'])->name('facebook.historyByUid');
Route::post('/history/delete_all', [FacebookHistoryController::class, 'deleteAllHistory'])->name('history.delete_all_history');
//2: Zalo History
Route::get('/zalo-history', [ZaloHistoryController::class, 'index']);



//request history
Route::get('/request-history/search', [RequestHistoryController::class, 'searchRequests']);
Route::get('/request-history/{id}', [RequestHistoryController::class, 'show']);
Route::get('/request-history', [RequestHistoryController::class, 'index']);
Route::delete('/request-history/{id}', [RequestHistoryController::class, 'delete']);
Route::post('/request-history/delete_all', [RequestHistoryController::class, 'deleteAll'])->name('request_history.delete_all');
