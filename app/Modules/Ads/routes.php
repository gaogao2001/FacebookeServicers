<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Ads\Controllers\AdsManagerController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;

Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {
    //social account
    Route::get('/ads_manager_page', [AdsManagerController::class, 'adsManagerPage']);
});

Route::get('/admin/facebook/ads-manager/data', [AdsManagerController::class, 'getAdsManager'])->name('admin.facebook.ads-manager.data');
Route::delete('/admin/facebook/adsmanager/{id}', [AdsManagerController::class, 'delete']);
Route::post('/admin/facebook/adsmanager/all-delete', [AdsManagerController::class, 'allDelete']);
Route::post('/filter_ads', [AdsManagerController::class, 'filterAds']);
Route::post('/clear_filter_ads', [AdsManagerController::class, 'clearFilter']);

Route::post('/export_account', [AdsManagerController::class, 'exportAccount']);