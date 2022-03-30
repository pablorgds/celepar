<?php

namespace Celepar\Light\Forms;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class FormsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'forms');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/forms'),
        ], 'forms');

        $this->publishes([
            __DIR__.'/assets' => public_path('vendor/forms'),
        ], 'public');

        $loader = AliasLoader::getInstance();
        $loader->alias('Messenger', \Jakjr\Messenger\MessengerFacade::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Jakjr\Messenger\MessengerServiceProvider::class);
    }
}
