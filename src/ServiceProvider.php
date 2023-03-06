<?php

namespace Butler\Health;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        if (app()->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/butler.php' => config_path('butler.php')], 'config');
        }

        if (! app()->routesAreCached() && config('butler.health.route')) {
            Route::get(config('butler.health.route'), Controller::class)->name('butler-health');
        }
    }
}
