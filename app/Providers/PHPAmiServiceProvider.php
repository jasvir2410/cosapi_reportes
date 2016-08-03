<?php

namespace Cosapi\Providers;

use Illuminate\Support\ServiceProvider;

class PHPAmiServiceProvider extends ServiceProvider
{

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        \App::bind('phpAMI', function()
        {
            return new \Cosapi\Helpers\phpAMI;
        });
    }
}
