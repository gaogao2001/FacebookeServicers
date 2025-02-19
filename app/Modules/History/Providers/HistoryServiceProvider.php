<?php

namespace App\Modules\History\Providers;

use Illuminate\Support\ServiceProvider;



class HistoryServiceProvider extends ServiceProvider
{
    public function register(): void 
    {
        $this->app->bind(
            \App\Modules\History\Repositories\Facebook\FacebookHistoryRepositoryInterface::class,
            \App\Modules\History\Repositories\Facebook\FacebookHistoryRepository::class
        );
        $this->app->bind(
            \App\Modules\History\Repositories\Zalo\ZaloHistoryRepositoryInterface::class,
            \App\Modules\History\Repositories\Zalo\ZaloHistoryRepository::class
        );
        $this->app->bind(
            \App\Modules\History\Repositories\System\SystemHistoryRepositoryInterface::class,
            \App\Modules\History\Repositories\System\SystemHistoryRepository::class
        );
        $this->app->bind(
            \App\Modules\History\Repositories\Request\RequestHistoryRepositoryInterface::class,
            \App\Modules\History\Repositories\Request\RequestHistoryRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/History/Views'), 'History');
    }
}
