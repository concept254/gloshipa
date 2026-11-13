<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Lets Blade views write Cms::setting('...') without an import.
        \Illuminate\Foundation\AliasLoader::getInstance()->alias('Cms', \App\Support\Cms::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
