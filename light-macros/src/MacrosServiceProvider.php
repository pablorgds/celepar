<?php

namespace Celepar\Light\Macros;

use Collective\Html\FormBuilder;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class MacrosServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/assets' => public_path('vendor/macros'),
        ], 'public');

        $loader = AliasLoader::getInstance();
        $loader->alias('Form', \Collective\Html\FormFacade::class);
        $loader->alias('Html', \Collective\Html\HtmlFacade::class);
        $loader->alias('cForm', \Celepar\Light\Macros\MacrosFacade::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Collective\Html\HtmlServiceProvider::class);

        $this->app->singleton('cform', function ($app) {

            $form = new MacroBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->getToken());

            return $form->setSessionStore($app['session.store']);

        });

    }
}
