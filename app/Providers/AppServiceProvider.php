<?php

namespace App\Providers;

use App\Models\SiteContent;
use App\Services\WebsiteCatalog;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['site.*', 'home-page', 'partials.ps-footer', 'partials.hero-carousel'], function ($view) {
            $view->with('site', SiteContent::localizedValues());
        });
        View::composer(['home-page', 'page-ke2', 'sewa-harian', 'menu-fnb'], function ($view) {
            $view->with(app(WebsiteCatalog::class)->data());
        });
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
