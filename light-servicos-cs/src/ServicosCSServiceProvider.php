<?php
namespace Celepar\Light\ServicosCS;

use Illuminate\Support\ServiceProvider;

/**
 * Class ServicosCSServiceProvider
 * @author Roberson A. Faria <roberson.faria@celepar.pr.gov.br>
 * @package Celepar\Light\ServicosCS
 */
class ServicosCSServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        \App::bind('ServicosCS', function()
        {
            return new ServicosCS();
        });
    }
}
