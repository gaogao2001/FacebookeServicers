<?php

namespace App\Modules\Facebook\Providers;

use Illuminate\Support\ServiceProvider;
use  App\Modules\Facebook\Repositories\Account\AccountRepositoryInterface;
use App\Modules\Facebook\Repositories\Account\AccountRepository;

class FacebookServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tải các view của module Facebook
        $this->loadViewsFrom(base_path('app/Modules/Facebook/Views'), 'Facebook');
    }
}
