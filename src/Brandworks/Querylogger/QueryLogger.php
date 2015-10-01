<?php namespace Brandworks\Querylogger;

use DB;
use Config;

class QueryLogger {
	/**
	 * All of the queries run against the connection.
	 *
	 * @var array
	 */
	protected $queryLog = array();

	/**
	 * Indicates whether queries are being logged.
	 *
	 * @var bool
	 */
	protected $loggingQueries = true;

	/**
	 * Create a new logger instance.
	 *
	 * @return void
	 */
	public function __construct() {
		if(Config::get('querylogger::disable_laravel_log')) {
			DB::disableQueryLog();
		}
		
		if(Config::get('querylogger::enabled')) {
			$this->loggingQueries = false;
		}
	}

	/**
	 * Log a query in the connection's query log.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @param  array   $called_at
	 * @param  float|null  $time
	 * @return void
	 */
	public function logQuery($query, $bindings, $called_at, $time = null) {
		if ( ! $this->loggingQueries) return;

		$this->queryLog[] = compact('query', 'bindings', 'called_at', 'time');
	}

	/**
	 * Get the connection query log.
	 *
	 * @return array
	 */
	public function getLog() {
		return $this->queryLog;
	}

	/**
	 * Clear the query log.
	 *
	 * @return void
	 */
	public function flushQueryLog()
	{
		$this->queryLog = array();
	}

	/**
	 * Enable the query log on the connection.
	 *
	 * @return void
	 */
	public function enableQueryLog()
	{
		$this->loggingQueries = true;
	}

	/**
	 * Disable the query log on the connection.
	 *
	 * @return void
	 */
	public function disableQueryLog()
	{
		$this->loggingQueries = false;
	}

	/**
	 * Determine whether we're logging queries.
	 *
	 * @return bool
	 */
	public function logging()
	{
		return $this->loggingQueries;
	}
}
