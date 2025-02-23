<?php

namespace App\Modules\CronTab\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\CronTab\Repositories\CrontabRepositoryInterface;
use App\Modules\CronTab\Repositories\CrontabRepository;

class CrontabServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CrontabRepositoryInterface::class, CrontabRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/CronTab/Views'), 'CronTab');
    }
}
