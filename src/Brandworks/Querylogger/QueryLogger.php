<?php namespace Brandworks\Querylogger;

use DB;
use Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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
	 * Indicates whether queries logs are written to file.
	 *
	 * @var bool
	 */
	protected $writingQueries = true;

	/**
	 * Indicates the file location of the query logs.
	 *
	 * @var String
	 */
	protected $logFile = 'logs/queries/queryLog.log';

	/**
	 * Indicates the file location of the query logs.
	 *
	 * @var bool
	 */
	protected $fillQueries = true;

	/**
	 * Indicates the file location of the query logs.
	 *
	 * @var Logger
	 */
	private $fileLogger = null;

	/**
	 * Create a new logger instance.
	 *
	 * @return void
	 */
	public function __construct() {
		if(Config::get('querylogger::disable_laravel_log') === true) {
			DB::disableQueryLog();
		}

		if(Config::get('querylogger::enabled') === false) {
			$this->loggingQueries = false;
		}

		if(Config::get('querylogger::logFile') && Config::get('querylogger::logFile') != "") {
			$this->logFile = Config::get('querylogger::logFile');
		}
		$this->logFile = storage_path($this->logFile);

		if(Config::get('querylogger::fill_queries') === false) {
			$this->fillQueries = false;
		}

		if(Config::get('querylogger::write_queries') === false) {
			$this->writingQueries = false;
		} else {
			$this->fileLogger = new Logger('Query Logs');
			$this->fileLogger->pushHandler(new StreamHandler($this->logFile, Logger::INFO));
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

		$this->queryLog[] = $query_data = compact('query', 'bindings', 'called_at', 'time');

		//write the queries
		if($this->writingQueries && $this->fileLogger != null) {
			if($this->fillQueries) {
				// format the bindings for insertion
				foreach ($bindings as $key => $binding) {
					if ($binding instanceof \DateTime) {
						$bindings[$key] = $binding->format('\'Y-m-d H:i:s\'');
					} else if (is_string($binding)) {
						$bindings[$key] = "'$binding'";
					}
				}

				// insert bindings into query
				$query = str_replace(array('%', '?'), array('%%', '%s'), $query);
				$query = vsprintf($query, $bindings);
			}

        	// add the record to the query log
        	$this->fileLogger->addInfo($query, $query_data);
		}
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
	public function flushQueryLog() {
		$this->queryLog = array();
	}

	/**
	 * Enable the query log on the connection.
	 *
	 * @return void
	 */
	public function enableQueryLog() {
		$this->loggingQueries = true;
	}

	/**
	 * Disable the query log on the connection.
	 *
	 * @return void
	 */
	public function disableQueryLog() {
		$this->loggingQueries = false;
	}

	/**
	 * Determine whether we're logging queries.
	 *
	 * @return bool
	 */
	public function logging() {
		return $this->loggingQueries;
	}

	/**
	 * Enable the writing of the queries.
	 *
	 * @return void
	 */
	public function enableWriting() {
		$this->writingQueries = true;
	}

	/**
	 * Disable the writing of the queries.
	 *
	 * @return void
	 */
	public function disableWriting() {
		$this->writingQueries = false;
	}

	/**
	 * Determine whether we're writing queries.
	 *
	 * @return bool
	 */
	public function writing() {
		return $this->writingQueries;
	}

	/**
	 * Set the log file
	 *
	 * @return void
	 */
	public function setLogFile($log_file) {
		$this->logFile = storage_path($log_file);

		//reset the writing to log file to the new location
		if($this->writingQueries) {
			$this->fileLogger = new Logger('Query Logs');
			$this->fileLogger->pushHandler(new StreamHandler($this->logFile, Logger::INFO));
		} else {
			$this->fileLogger = null;
		}
	}

	/**
	 * Get the log file.
	 *
	 * @return void
	 */
	public function logFile() {
		//return the original log file - not the full path
		return str_replace(storage_path(), "", $this->logFile);
	}

	/**
	 * Enable the filling of the queries.
	 *
	 * @return void
	 */
	public function enableFilling() {
		$this->fillQueries = true;
	}

	/**
	 * Disable the filling of the queries.
	 *
	 * @return void
	 */
	public function disableFilling() {
		$this->fillQueries = false;
	}

	/**
	 * Determine whether we're filling queries with the bindings.
	 *
	 * @return bool
	 */
	public function filling() {
		return $this->fillQueries;
	}
}
