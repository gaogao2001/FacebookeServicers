<?php

namespace App\Modules\ConfigAuto\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\ConfigAuto\Repositories\ConfigAutoRepository;
use App\Modules\ConfigAuto\Repositories\ConfigAutoRepositoryInterface;

class ConfigAutoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ConfigAutoRepositoryInterface::class, ConfigAutoRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/ConfigAuto/Views'), 'ConfigAuto');
    }
}
