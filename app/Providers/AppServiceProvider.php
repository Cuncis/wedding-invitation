<?php

namespace App\Providers;

use App\Events\PaymentSucceeded;
use App\Listeners\PublishInvitationAfterPayment;
use App\Listeners\SendPaymentReceiptEmail;
use App\Services\InvitationPublisher;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\Payment\MidtransGateway;
use App\Services\PaymentService;
use App\Services\PricingService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;
use Midtrans\Config as MidtransConfig;

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

        $this->app->singleton(MidtransGateway::class);

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService($app->make(MidtransGateway::class));
        });

        $this->app->singleton(NotificationService::class);

        $this->app->singleton(InvitationPublisher::class, function ($app) {
            return new InvitationPublisher($app->make(NotificationService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$clientKey = config('midtrans.client_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = true;

        Event::listen(PaymentSucceeded::class, PublishInvitationAfterPayment::class);
        Event::listen(PaymentSucceeded::class, SendPaymentReceiptEmail::class);

        Schedule::command('orders:expire')->hourly();
    }
}
