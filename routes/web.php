<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Site;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MyShipmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingController;
use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

// Public home page with the hero and tracking form
Route::get('/', [Site\SiteController::class, 'home'])->name('home');

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

// ---------------------------------------------------------------------------
// Public website pages (content is managed in the admin under "Website")
// ---------------------------------------------------------------------------
Route::controller(Site\SiteController::class)->name('site.')->group(function () {
    Route::get('/about', 'about')->name('about');
    Route::get('/services', 'services')->name('services');
    Route::get('/services/{slug}', 'service')->name('service');
    Route::get('/faqs', 'faqs')->name('faqs');
    Route::get('/testimonials', 'testimonials')->name('testimonials');
    Route::get('/team', 'team')->name('team');
    Route::get('/clients', 'clients')->name('clients');
    Route::get('/news', 'news')->name('news');
    Route::get('/news/{slug}', 'post')->name('post');
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactStore')->middleware('throttle:6,1')->name('contact.store');
    Route::get('/quote', 'quote')->name('quote');
    Route::post('/quote', 'quoteStore')->middleware('throttle:6,1')->name('quote.store');
    Route::get('/sitemap.xml', 'sitemap')->name('sitemap');
});

Route::post('/newsletter', [Site\NewsletterController::class, 'subscribe'])->middleware('throttle:6,1')->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [Site\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Super admin: website content
Route::middleware(['auth', EnsureAccountIsActive::class, EnsureUserIsAdmin::class])
    ->prefix('admin/website')
    ->name('admin.website.')
    ->group(function () {
        Route::get('/', [Admin\ContentController::class, 'hub'])->name('hub');
        Route::get('/settings', [Admin\SettingsController::class, 'edit'])->name('settings');
        Route::put('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::get('/inbox', [Admin\InboxController::class, 'index'])->name('inbox');
        Route::get('/inbox/{inquiry}', [Admin\InboxController::class, 'show'])->name('inbox.show');
        Route::delete('/inbox/{inquiry}', [Admin\InboxController::class, 'destroy'])->name('inbox.destroy');
        Route::get('/subscribers', [Admin\InboxController::class, 'subscribers'])->name('subscribers');
        Route::get('/subscribers/export', [Admin\InboxController::class, 'export'])->name('subscribers.export');
        Route::delete('/subscribers/{subscriber}', [Admin\InboxController::class, 'destroySubscriber'])->name('subscribers.destroy');

        Route::get('/{resource}', [Admin\ContentController::class, 'index'])->name('index');
        Route::get('/{resource}/create', [Admin\ContentController::class, 'create'])->name('create');
        Route::post('/{resource}', [Admin\ContentController::class, 'store'])->name('store');
        Route::get('/{resource}/{id}/edit', [Admin\ContentController::class, 'edit'])->name('edit');
        Route::put('/{resource}/{id}', [Admin\ContentController::class, 'update'])->name('update');
        Route::delete('/{resource}/{id}', [Admin\ContentController::class, 'destroy'])->name('destroy');
    });

// Custom pages created in the admin. MUST stay last so real routes win.
Route::get('/{slug}', [Site\SiteController::class, 'page'])
    ->where('slug', '[A-Za-z0-9\-_]+')
    ->name('site.page');
