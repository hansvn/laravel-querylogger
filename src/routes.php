<?php
Route::get(Config::get('querylogger::add_route'), function() {
	$files_in_dir = @scandir(QueryLogger::storageFolder());
	$data['files'] = array();

	if($files_in_dir) {
		foreach ($files_in_dir as $file) {
			if(strpos($file, '.slzd') !== false) {
				array_push($data['files'], $file);
			}
		}
	} else {
		$data['files']['nothing'] = 'No serialized log files found!';
	}

	return View::make('querylogger::logfiles', $data);
});

Route::get(Config::get('querylogger::add_route').'/file/{file_name}', function($file_name) {
	if(strpos($file_name, '/') === false) {
		$file_name = QueryLogger::storageFolder().$file_name;
	}

	return View::make('querylogger::query_log', array('file' => unserialize(file_get_contents($file_name))));
});