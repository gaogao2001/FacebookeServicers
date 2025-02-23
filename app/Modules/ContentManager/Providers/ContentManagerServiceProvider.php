<?php

namespace App\Modules\ContentManager\Providers;

use App\Modules\ContentManager\Repositories\ContentManagerRepository;
use App\Modules\ContentManager\Repositories\ContentManagerRepositoryInterface;
use Illuminate\Support\ServiceProvider;



class ContentManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ContentManagerRepositoryInterface::class, ContentManagerRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/ContentManager/Views'), 'ContentManager');
    }
}
