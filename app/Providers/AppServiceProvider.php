<?php

namespace App\Providers;

use App\Services\OrderService;
use App\Services\PricingService;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PricingService::class);

        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService($app->make(PricingService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schedule::command('orders:expire')->hourly();
    }
}
