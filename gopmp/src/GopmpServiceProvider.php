<?php namespace Celepar\Gopmp;

use Illuminate\Support\ServiceProvider;

class GopmpServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
        $this->publishes([
            __DIR__.'/config/gopmp.php' => config_path('gopmp.php'),
        ]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        \Route::get('gopmp', 'Celepar\Gopmp\MonitorController@check');

        $this->mergeConfigFrom(
            __DIR__.'/config/gopmp.php', 'gopmp'
        );
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
