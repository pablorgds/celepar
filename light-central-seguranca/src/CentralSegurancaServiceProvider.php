<?php

namespace Celepar\Light\CentralSeguranca;

use Illuminate\Support\ServiceProvider;
use Auth;


class CentralSegurancaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/central-seguranca.php' => config_path('central-seguranca.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/central-seguranca'),
        ], 'views');

        Auth::extend('central-seguranca', function($app)
        {
            return new UserProvider($app);
        });

        require __DIR__ .'/routes.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
