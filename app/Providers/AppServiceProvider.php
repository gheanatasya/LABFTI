<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Broadcasting\FcmChannel;
use Illuminate\Support\Facades\Notification;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //
    }
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
}
