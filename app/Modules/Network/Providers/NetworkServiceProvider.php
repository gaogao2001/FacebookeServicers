<?php

namespace App\Modules\Network\Providers;

use App\Modules\Network\Repositories\NetworkRepository;
use App\Modules\Network\Repositories\NetworkRepositoryInterface;
use App\Modules\Network\Repositories\ProxyV6\ProxyV6Repository;
use App\Modules\Network\Repositories\ProxyV6\ProxyV6RepositoryInterface;
use GuzzleHttp\Handler\Proxy;
use HoangquyIT\ProxyV6;
use Illuminate\Support\ServiceProvider;



class NetworkServiceProvider extends ServiceProvider
{
   

    public function register(): void 
    {
        $this->app->bind(NetworkRepositoryInterface::class, NetworkRepository::class);
        $this->app->bind(ProxyV6RepositoryInterface::class, ProxyV6Repository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/Network/Views'), 'Network');

        $settings = app(NetworkRepositoryInterface::class)->findAll();

        config(['network_settings.settings' => $settings]);
        
    }
}
