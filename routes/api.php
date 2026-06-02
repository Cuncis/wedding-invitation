<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationConfigController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\InvitationPublishController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\PublicInvitationController;
use App\Http\Controllers\RsvpController;
use App\Http\Controllers\ThemeSwitcherController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('/register', [RegisterController::class, 'store']);
        Route::post('/login', [LoginController::class, 'store']);
    });

    Route::get('/invitations/{slug}', PublicInvitationController::class);
    Route::get('/pricing', PricingController::class);
});

Route::post('/webhooks/midtrans', WebhookController::class)
    ->middleware('throttle:60,1');

Route::post('/invitations/{invitation}/rsvp', [RsvpController::class, 'store'])
    ->middleware('throttle:rsvp');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/auth/logout', LogoutController::class);
    Route::post('/auth/verify-email', VerifyEmailController::class);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::apiResource('invitations', InvitationController::class);
    Route::patch('/invitations/{invitation}/config', InvitationConfigController::class);
    Route::post('/invitations/{invitation}/publish', InvitationPublishController::class);
    Route::patch('/invitations/{invitation}/theme', ThemeSwitcherController::class);

    Route::apiResource('orders', OrderController::class);
    Route::post('/orders/{order}/payments', [PaymentController::class, 'create']);
});
