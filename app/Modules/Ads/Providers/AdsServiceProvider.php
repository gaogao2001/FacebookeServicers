<?php

namespace App\Modules\Ads\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Ads\Repositories\AdsManager\AdsManagerRepositoryInterface;
use App\Modules\Ads\Repositories\AdsManager\AdsManagerRepository;


class AdsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AdsManagerRepositoryInterface::class, AdsManagerRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/Ads/Views'), 'Ads');
    }
}
