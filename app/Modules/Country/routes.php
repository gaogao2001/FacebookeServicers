<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Country\Controllers\CountryController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;


Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {
    //social account
    Route::get('/contry_page', [CountryController::class, 'countryPage']);
});
Route::get('/admin/facebook/countries/data', [CountryController::class, 'getCountries'])->name('admin.facebook.countries.data');
Route::delete('/admin/facebook/country/{id}', [CountryController::class, 'delete']);
Route::post('/admin/facebook/country/all-delete', [CountryController::class, 'allDelete']);