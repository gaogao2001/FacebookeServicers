<?php

namespace App\InterfaceModules\DeviceEmulator\Android\Providers;

use Illuminate\Support\ServiceProvider;
use App\InterfaceModules\DeviceEmulator\Android\Repositories\AndroidRepository;
use App\InterfaceModules\DeviceEmulator\Android\Repositories\AndroidRepositoryInterface;


class AndroidServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AndroidRepositoryInterface::class, AndroidRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tải các view của module DeviceEmulator
        $this->loadViewsFrom(base_path('app/InterfaceModules/DeviceEmulator/Android/Views'), 'Android');
    }
}
