<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WooCommerceService;

class WooCommerceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(WooCommerceService::class, function ($app) {
            return new WooCommerceService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
