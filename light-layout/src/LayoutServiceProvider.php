<?php

namespace Celepar\Light\Layout;

use Illuminate\Support\ServiceProvider;

class LayoutServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'layout');

        $this->publishes([
            __DIR__.'/views/shared' => base_path('resources/views/vendor/layout/shared')
        ]);

        $this->publishes([
            __DIR__.'/config/layout.php' => config_path('layout.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/assets' => public_path('vendor/layout'),
        ], 'public');

        if (@$_COOKIE['sidebar_closed'] == '1') {
            view()->share('sidebarClosed', ' page-sidebar-closed');
        }

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/layout.php', 'layout'
        );
    }
}
