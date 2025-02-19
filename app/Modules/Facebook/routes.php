<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Facebook\Controllers\FacebookController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;

Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {
    Route::get('/facebook_page', [FacebookController::class, 'facebookPage'])->name('facebook.pages');
 
});

Route::middleware([SessionTimeout::class])->group(function () {
    Route::get('/facebook-edit/{id}', [FacebookController::class, 'show'])->name('facebook.edit');
    Route::put('/facebook-update/{id}', [FacebookController::class, 'update'])->name('facebook.update');
    Route::post('/facebook-delete/{id}', [FacebookController::class, 'destroy'])->name('facebook.destroy');
    Route::get('/facebooks/search', [FacebookController::class, 'searchAccounts']);
    Route::get('/facebook-show-json/{id}', [FacebookController::class, 'showJson'])->name('facebook.showJson');
    Route::get('/facebook_page/load_more', [FacebookController::class, 'loadMoreAccounts'])->name('facebook.load_more');
    Route::post('/send-accounts', [FacebookController::class, 'TransferAccountServer'])->name('facebook.send_accounts');
    Route::post('/change-group', [FacebookController::class, 'changeAccountGroup'])
        ->name('facebook.change_account_group');
    Route::post('/facebook/change-status', [FacebookController::class, 'changeStatus'])->name('facebook.change_status');
    Route::delete('/delete-accounts', [FacebookController::class, 'deleteAccounts'])->name('facebook.delete_accounts');
    Route::post('/facebook/delete_all_accounts', [FacebookController::class, 'deleteAllAccounts'])->name('facebook.delete_all_accounts');

    Route::get('/CreatePassword', [FacebookController::class, 'CreatePassword'])->name('facebook.createPassword');
    Route::get('/CheckLiveUid/{uid}', [FacebookController::class, 'checkLiveUid']);
    Route::get('/LoadAllFacebook', [FacebookController::class, 'loadAllFacebook']);
    Route::post('/facebook/update-coordinates/{id}', [FacebookController::class, 'updateCoordinates'])->name('facebook.updateCoordinates');
    Route::get('/proxySplit', [FacebookController::class, 'proxySplit'])->name('facebook.proxySplit');
    Route::post('/ImportAccount', [FacebookController::class, 'importAccount'])->name('facebook.importAccount');
    Route::post('/facebook_page/filter', [FacebookController::class, 'filterAccounts'])->name('facebook.filter');
    Route::post('/facebook/clearFilter', [FacebookController::class, 'clearFilter'])->name('facebook.clearFilter');
    Route::get('/facebook/fix-birthday', [FacebookController::class, 'fixBirthday'])->name('facebook.fix_birthday');
    Route::post('facebook_page/showpassword' , [FacebookController::class, 'showPassword'])->name('facebook.showPassword');
    Route::post('/export-accounts', [FacebookController::class, 'exportAccounts'])->name('facebook.export_accounts');
    Route::post('/import-accounts', [FacebookController::class, 'importAccountByFile'])->name('facebook.import_accounts');
    Route::post('/facebook/update-network-use', [FacebookController::class, 'updateNetworkUse'])->name('facebook.update_network_use');
    Route::post('/facebook/update_networkuse_by_proxy_list', [FacebookController::class, 'updateNetworkUseByProxyList'])->name('facebook.update_networkuse_by_proxy_list');

    //message
    Route::get('/multi_message_comment_page', [FacebookController::class, 'multiMessageCommentPage'])->name('facebook.multi_message_comment_page');
});
