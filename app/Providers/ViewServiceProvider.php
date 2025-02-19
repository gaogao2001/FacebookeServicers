<?php

namespace App\Providers;

use App\Repositories\SiteManager\SiteManagerRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(SiteManagerRepositoryInterface $siteManagerRepository): void
    {
       
        View::share('siteManager');
    }
}
