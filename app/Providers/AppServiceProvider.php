<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use HoangquyIT\MongoDB\Client;
use Illuminate\Support\Facades\View;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\DatabaseManager;
use App\Http\View\Composers\ProfileBanerComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('mongo', function () {
            return new Client("mongodb://localhost:27017");
        });
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::except([
            'users',
            'users/*',
            'roles',
            'roles/*',
            'history',
            'history/*',
            'get-video',
            '/reboot',
            '/shutdown',
            'links',
            'links/*',
            'updateImage/*',
            'post-video',
            'post-reels',
            'live-video',
            '/file-manager/create-folder',
            '/file-manager/update-path',
            '/facebook_page/filter',
            '/facebook/fix-birthday',
            'Android/add_friend/*',
            'Android/remove_friend/*',
            'Android/update_post/*',
            '/facebook-delete/*',
            'Android/Join-groups/*',
            '/Android/like-follow/*',
            '/notifications',
            '/notifications/*',
            '/delete-notifications',
            '/document',
            '/document/*',
        ]);

        $this->loadViewsFrom(base_path('app/Modules/Auth/Views'), 'Auth');
        $this->loadViewsFrom(base_path('app/Modules/Dashboard/Views'), 'Dashboard');
        $this->loadViewsFrom(base_path('app/Modules/Roles/Views'), 'Roles');
        $this->loadViewsFrom(base_path('app/Modules/User/Views'), 'User');
        $this->loadViewsFrom(base_path('app/Modules/SiteManager/Views'), 'SiteManager');
        $this->loadViewsFrom(base_path('app/Modules/BackupData/Views'), 'BackupData');
        $this->loadViewsFrom(base_path('app/Modules/FileManager/Views'), 'FileManager');
        $this->loadViewsFrom(base_path('app/Modules/Map/Views'), 'Map');
        $this->loadViewsFrom(base_path('app/Modules/Notification/Views'), 'Notification');

    }
}
