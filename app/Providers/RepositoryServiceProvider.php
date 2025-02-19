<?php

namespace App\Providers;

use App\Repositories\Account\AccountRepository;
use App\Repositories\Account\AccountRepositoryInterface;
use App\Repositories\Roles\RoleRepositoryInterface;
use App\Repositories\Roles\RoleRepository;
use App\Repositories\SiteManager\SiteManagerRepositoryInterface;
use App\Repositories\SiteManager\SiteManagerRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * các bindings của role ; site; account dùng dể tương tác với giao diện ; phân quyền project
     */
    public function register(): void
    {
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(SiteManagerRepositoryInterface::class, SiteManagerRepository::class);
    }
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
