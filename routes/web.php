<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MyShipmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingController;
use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

// Public home page with the hero and tracking form
Route::get('/', [HomeController::class, 'index'])->name('home');

// Visitors who are not signed in
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:20,1');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:6,1');

    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

// Logout only needs a session, so it also works for accounts that were just suspended
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Signed-in, approved users (clients and admins)
Route::middleware(['auth', EnsureAccountIsActive::class])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/shipments', [MyShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/track', [TrackingController::class, 'show'])->name('track');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
});

// Super admin only
Route::middleware(['auth', EnsureAccountIsActive::class, EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/status', [Admin\UserController::class, 'updateStatus'])->name('users.status');

        Route::resource('shipments', Admin\ShipmentController::class);
        Route::post('/shipments/{shipment}/events', [Admin\ShipmentEventController::class, 'store'])->name('shipments.events.store');
        Route::delete('/shipments/{shipment}/events/{event}', [Admin\ShipmentEventController::class, 'destroy'])->name('shipments.events.destroy');
    });
