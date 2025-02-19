<?php

namespace App\Modules\EmailScan\Providers;

use App\Modules\EmailScan\Repositories\EmailScanRepository;
use App\Modules\EmailScan\Repositories\EmailScanRepositoryInterface;
use Illuminate\Support\ServiceProvider;



class EmailScanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmailScanRepositoryInterface::class,EmailScanRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/EmailScan/Views'), 'EmailScan');
    }
}
