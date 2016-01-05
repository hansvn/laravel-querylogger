<?php 
return array(
	//Enable the query logger
    'enabled' => true,

    //Disable the laravel query log
    // This is for performance - we do not want to log the queries twice in the memory
    'disable_laravel_log' => true,

    //Wether or not fill the bindings in the query (in the log file)
	'fill_queries' => true,

	//Wether or not to write the queries to a log file
	'write_queries' => true,

	//The name and location of the log file
    'logPath' => 'logs/queries/',
    'logFile' => 'queryLog.log',


    // The following config options are used to enable the (visual) result of the queries
    //Whether or not to save all the requests queries to view them afterwards (e.g. in a table)
    'store_serialized' => false,

    //Wether or not to add a route to view the results of all queries
    //to add the route: set this to the string of the route location
    //best is to disable this in production! - defaulted with false
    'add_route' => false, //e.g.: 'add_route' => 'view_logs'
);