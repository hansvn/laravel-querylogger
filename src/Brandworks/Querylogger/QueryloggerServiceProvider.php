<?php namespace Brandworks\Querylogger;

use Illuminate\Support\ServiceProvider;

class QueryloggerServiceProvider extends ServiceProvider {

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
		$this->package('brandworks/querylogger', 'querylogger');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('querylogger', function($app) {
			return new QueryLogger();
		});

		// Shortcut so developers don't need to add an Alias in app/config/app.php
		$this->app->booting(function() {
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('QueryLogger', 'Brandworks\Querylogger\Facades\QueryLogger');
		});

		//the event listener for illuminate queries
		$logger = $this->app['querylogger'];

		if($logger->logging()) {
			\Event::listen('illuminate.query', function($query, $bindings, $time, $name) use ($logger) {
				$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
				$file_found = false;
				$current_file = current($bt);

				while (!$file_found) {
					if(array_key_exists('file', $current_file) && strpos($current_file['file'], 'vendor') === false && strpos($current_file['file'], 'compiled.php') === false) {
						$file_found = true;
					}

					if(!$file_found) $current_file = next($bt);
				}

				$logger->logQuery($query, $bindings, $current_file, $time);
			});
		}
	}

}
