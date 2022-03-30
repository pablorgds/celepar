<?php

namespace Celepar\Light\Menu;

use Celepar\Light\Menu\ViewComposers\Menu;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services
     *
     * @return void
     */
    public function boot()
    {
        if (! file_exists(app_path('menu.php'))) {
            return;
        }

        $loader = AliasLoader::getInstance();
        $loader->alias('Menu', Menu::class);

        view()->composer(
            'layout::master.sidebar', Menu::class
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if (! file_exists(app_path('menu.php'))) {
            return;
        }
        $this->app->register(\Tlr\Menu\Laravel\MenuServiceProvider::class);
    }
}
