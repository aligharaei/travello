<?php

namespace App\Providers;

use App\Interfaces\TourProviderInterface;
use App\Services\HeavenlyTourProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TourProviderInterface::class, HeavenlyTourProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        require_once app_path('helpers.php');
        Schema::defaultStringLength(191);
    }
}
