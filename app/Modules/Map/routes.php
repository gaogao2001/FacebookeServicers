<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Map\Controller\MapController;


Route::get('/map', [MapController::class, 'showMap'])->name('map.show');
Route::post('/map', [MapController::class, 'handleMapData'])->name('map.handle');