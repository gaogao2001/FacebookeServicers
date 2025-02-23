<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Role\Controllers\RoleController;


Route::get('/roles', [RoleController::class, 'index']);
Route::post('/roles', [RoleController::class, 'addRole']);
Route::get('/roles/{id}', [RoleController::class, 'show']);
Route::put('/roles/{id}', [RoleController::class, 'update']);
Route::delete('/roles/{id}', [RoleController::class, 'delete']);