<?php
namespace App\Modules\ImageVideo\Providers;

use Illuminate\Support\ServiceProvider;

class VideoImageServiceProvider extends ServiceProvider
{
    public function register(): void 
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(base_path('app/Modules/ImageVideo/Views'), 'VideoImage');
       
    }
}