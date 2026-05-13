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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/register', [RegisterController::class, 'store']);
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/webhooks/midtrans', WebhookController::class);
Route::get('/invitations/{slug}', PublicInvitationController::class);
Route::post('/invitations/{invitation}/rsvp', [RsvpController::class, 'store']);
Route::get('/pricing', PricingController::class);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/auth/logout', LogoutController::class);
    Route::post('/auth/verify-email', VerifyEmailController::class);

    Route::get('/dashboard', DashboardController::class);
    Route::apiResource('invitations', InvitationController::class);
    Route::patch('/invitations/{invitation}/config', InvitationConfigController::class);
    Route::post('/invitations/{invitation}/publish', InvitationPublishController::class);
    Route::patch('/invitations/{invitation}/theme', ThemeSwitcherController::class);

    Route::apiResource('orders', OrderController::class);
    Route::post('/orders/{order}/payments', [PaymentController::class, 'create']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
