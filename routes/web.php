<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BuilderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationShowController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RsvpController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest auth routes
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

// Authenticated routes
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/create', [DashboardController::class, 'create'])->name('dashboard.create');

    Route::get('/builder/{invitation}/edit', [BuilderController::class, 'edit'])->name('builder.edit');
    Route::put('/builder/{invitation}/config', [BuilderController::class, 'updateConfig'])->name('builder.config');
    Route::get('/builder/{invitation}/preview', [BuilderController::class, 'preview'])->name('builder.preview');

    Route::get('/invitations/{invitation}/checkout', [OrderController::class, 'showCheckout'])->name('checkout.show');
    Route::post('/invitations/{invitation}/checkout', [OrderController::class, 'storeCheckout'])->name('checkout.store');

    Route::get('/orders/{order}/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::get('/orders/{order}', [OrderController::class, 'showOrder'])->name('orders.show');

    Route::get('/rsvp/{invitation}/export', [RsvpController::class, 'export'])->name('rsvp.export');
    Route::get('/rsvp/{invitation}/responses', [RsvpController::class, 'index'])->name('rsvp.index');
});

// Public RSVP submit (no auth)
Route::post('/rsvp/{slug}', [RsvpController::class, 'store'])->name('rsvp.store');

// Public invitation pages — MUST be last to avoid catching other routes
Route::get('/{slug}', InvitationShowController::class)
    ->name('invitation.show')
    ->where('slug', '[a-z][a-z0-9-]*');
