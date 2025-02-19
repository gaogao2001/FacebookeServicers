<?php

namespace App\Modules\Zalo\Providers;

use Illuminate\Support\ServiceProvider;



class ZaloServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Zalo\Repositories\ZaloRepositoryInterface::class,
            \App\Modules\Zalo\Repositories\ZaloRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/Zalo/Views'), 'Zalo');

    }
}