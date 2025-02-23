<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EmailScanController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\API\LinkController;
use App\Http\Controllers\API\AccountController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//post Email
Route::post('/email-scan', [EmailScanController::class, 'addEmailScan']);
//post links
Route::post('/add-url-facebook', [LinkController::class, 'addNewUrlFacebook']);

Route::post('/account_facebook', [AccountController::class, 'addAccountFacebook']);
Route::get('/count_facebook', [AccountController::class, 'CountAccountFacebook']);

