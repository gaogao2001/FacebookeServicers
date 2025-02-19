<?php

namespace App\Modules\ServiceController\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceControllerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
     //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/ServiceController/Views'), 'ServiceController');
    }
}