<?php

namespace Celepar\Light\Neocep\Providers;

use Celepar\Light\Neocep\Facade\Neocep;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class NeocepServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/neocep.php' => config_path('neocep.php'),
        ], 'config');

        $loader = AliasLoader::getInstance();
        $loader->alias('Neocep', Neocep::class);

        require(__DIR__."/../Http/routes.php");
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/neocep.php', 'neocep'
        );

        $this->app->bind('neocep', 'Celepar\Light\Neocep\NeocepRest' );
    }
}
