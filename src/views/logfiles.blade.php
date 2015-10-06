<!DOCTYPE html>
<html>
<head>
	<title>Query Log Files</title>
	<style type="text/css">
	* {
		font-family: sans-serif;
	}
	</style>
</head>
<body>
	<h1>Log Files</h1>
	@if(isset($files))
	<?php $route = Config::get('querylogger::route'); //store in variable so we don't get it everytime over and over again?>
	@foreach($files as $file) 
		<a href='{{URL::to("$route/file/$file")}}'>{{$file}}</a><br />
	@endforeach
	@endif	
</body>
</html>