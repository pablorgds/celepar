<?php

namespace Celepar\Light\Shortcut;

use Celepar\Light\Shortcut\ViewComposers\Shortcut;
use Illuminate\Support\ServiceProvider;

class ShortcutServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(
            'layout::master.header', Shortcut::class
        );
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
