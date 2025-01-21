<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;
use App\Services\SupportWebp;

class ThemeServiceProvider extends SageServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->singleton(SupportWebp::class, function ($app) {
            return new SupportWebp();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        $this->app->make(SupportWebp::class);
    }
}
