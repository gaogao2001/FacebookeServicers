<?php

namespace App\Modules\Fanpage\Providers;

use App\Modules\Fanpage\Repositories\FanpageManagerRepositoryInterface;
use App\Modules\Fanpage\Repositories\FanpageManagerRepository;
use Illuminate\Support\ServiceProvider;



class FanpageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FanpageManagerRepositoryInterface::class, FanpageManagerRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/Fanpage/Views'), 'Fanpage');
    }
}
