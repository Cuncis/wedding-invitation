<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
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

    Route::get('/invitations/{invitation}/checkout', [OrderController::class, 'showCheckout'])->name('checkout.show');
    Route::post('/invitations/{invitation}/checkout', [OrderController::class, 'storeCheckout'])->name('checkout.store');

    Route::get('/orders/{order}', [OrderController::class, 'showOrder'])->name('orders.show');
});
