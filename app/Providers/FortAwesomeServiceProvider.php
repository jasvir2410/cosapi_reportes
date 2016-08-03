<?php

namespace Cosapi\Providers;

use Illuminate\Support\ServiceProvider;

class FortAwesomeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../vendor/fortawesome/font-awesome/css' => public_path('vendor/fortawesome/css'),
        ], 'public');

        $this->publishes([
                __DIR__ . '/../../vendor/fortawesome/font-awesome/fonts' => public_path('vendor/fortawesome/fonts'),
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
