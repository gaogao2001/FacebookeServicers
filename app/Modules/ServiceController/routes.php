<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ServiceController\Controller\ServiceController;

Route::get('/service_manager_page', [ServiceController::class, 'index']);
Route::get('/service_manager_page/history', [ServiceController::class, 'getHistory']);