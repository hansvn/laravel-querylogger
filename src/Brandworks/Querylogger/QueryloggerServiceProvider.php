<?php namespace Brandworks\Querylogger;

use Illuminate\Support\ServiceProvider;
use Config;
use App;

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

		if(Config::get('querylogger::add_route')) {
			//add log file to the routes
			include_once __DIR__.'/../../routes.php';
		}
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

		//explicitly register the config file
		$this->app['config']->package('brandworks/querylogger', __DIR__.'/../../config');

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

		if(Config::get('querylogger::store_serialized')) {
			//do this in app finish so this wont affect the users response time (response was already sent to client)
			App::finish(function($request, $response) use ($logger) {
				//we do not want to track the page that displays the logs
				if(strpos($request->getRequestUri(), Config::get('querylogger::route')) === false) {
					$queryLog = $logger->getLog();

					$sorted_queries = array();
					$total = 0;

					foreach ($queryLog as $query) {
						//set time with bindings
						$time_with_bindings = array(
							'time' => $query['time'],
							'bindings' => $query['bindings'],
						);

						if(array_key_exists('called_at', $query)) {
							$time_with_bindings['location'] = $query['called_at'];
						}

						if(array_key_exists($query['query'], $sorted_queries)) {
							array_push($sorted_queries[$query['query']], $time_with_bindings);
						}
						else {
							$sorted_queries[$query['query']] = array($time_with_bindings);
						}

						$total++;
					}

					$summary = array(
						'uri' => $request->getRequestUri(),
						'total' => $total,
						'max_times' => $sorted_queries,
					);

					//append and store file
					//check if the folder exists
					if (!file_exists(storage_path(Config::get('querylogger::logPath')))) {
						mkdir(storage_path(Config::get('querylogger::logPath')), 0774, true);
					}

					//get the file location and add 'serialized' (slzd) extension
					$log_file = $logger->storageLogFile().".slzd";
					if(!file_exists($log_file)) {
						//initialize empty array
						$total_log = array();
					}
					else {
						//get previous array to append to
						$log_content = file_get_contents($log_file);
						$total_log = @unserialize($log_content);
					}

					if($total_log !== false && is_array($total_log)) {
						//unserializing succeeded
						array_push($total_log, $summary);
					}
					else {
						$total_log = array($summary);
					}

					file_put_contents($log_file, serialize($total_log));

					//if we have more than 128 request logs in one file: create a new one
					if(count($total_log) > 128) {
						rename($log_file, str_replace('.slzd', '_'.date('YmdHis').'.slzd', $log_file));
					}
				}//end if check on request
			});
		}
	}

}
