<?php

namespace App\Modules\Document\Providers;

use Illuminate\Support\ServiceProvider;

class DocumentServiceProvider extends ServiceProvider
{
    public function boot()
    {
       
    }

    public function register()
    {
        $this->loadViewsFrom(base_path('app/Modules/Document/Views'), 'Document');
    }
}