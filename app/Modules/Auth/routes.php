<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Controllers\LoginController;

Route::get('/login', [LoginController::class, 'showFormLogin']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [LoginController::class, 'showFormRegister']);
Route::post('/register', [LoginController::class, 'register'])->name('register');
Route::get('/check-ip', [LoginController::class, 'check']);
