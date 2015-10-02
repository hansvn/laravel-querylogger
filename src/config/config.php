<?php 
return array(
	//Enable the query logger
    'enabled' => true,

    //Disable the laravel query log
    // This is for performance - we do not want to log the queries twice in the memory
    'disable_laravel_log' => true;

    //Wether or not fill the bindings in the query (in the log file)
	'fill_queries' => true,

	//Wether or not to write the queries to a log file
	'write_queries' => true,

	//The name and location of the log file
    'logFile' => "logs/queries/queryLog.log",
);