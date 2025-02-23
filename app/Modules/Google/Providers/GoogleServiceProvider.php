<?php

namespace App\Modules\Google\Providers;

use Illuminate\Support\ServiceProvider;



class GoogleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Google\Repositories\AccountRepositoryInterface::class,
            \App\Modules\Google\Repositories\AccountRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/Google/Views'), 'Google');
    }
}
