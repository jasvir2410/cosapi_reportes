<?php

namespace Cosapi\Providers;

use Illuminate\Support\ServiceProvider;

class IoniconsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../vendor/driftyco/ionicons/css' => public_path('vendor/ionicons/css'),
        ], 'public');

        $this->publishes([
                __DIR__ . '/../../vendor/driftyco/ionicons/fonts' => public_path('vendor/ionicons/fonts'),
            ], 'public');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
