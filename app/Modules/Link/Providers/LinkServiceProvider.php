<?php

namespace App\Modules\Link\Providers;

use App\Modules\Link\Repositories\LinkRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Modules\Link\Repositories\LinkRepository;
class LinkServiceProvider extends ServiceProvider
{
    public function register(): void 
    {
        $this->app->bind(LinkRepositoryInterface::class, LinkRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/Link/Views'), 'Link');
    }
}
