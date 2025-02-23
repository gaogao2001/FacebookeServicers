<?php

namespace App\Modules\Country\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Country\Repositories\CountryRepositoryInterface;
use App\Modules\Country\Repositories\CountryRepository;



class CountryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/Country/Views'), 'Country');

    }
}
