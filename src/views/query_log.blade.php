<!DOCTYPE html>
<html>
<head>
	<title>Query Log Files</title>

	<style type="text/css">
	* {
		font-family: sans-serif;
	}
	table.legend {
		border-spacing: 0;
    	border-collapse: collapse;
	}
	table.legend th,
	table.legend td {
		border: 1px solid #ccc;
		padding: 6px;
	}
	table.legend tr:not(:first-child) th {
		text-align: right;
	}
	</style>

	<link href="https://cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
</head>
<body>
	<h1>Log Files</h1>

	<table class="legend">
		<tbody>
			<tr><th colspan="2">Legend</th></tr>
			<tr>
				<th>Time</th>
				<td><font color="#f57900"><b>Orange</b></font></td>
			</tr>
			<tr>
				<th>Query</th>
				<td><font color="black">Black</font></td>
			</tr>
			<tr>
				<th>Duplicate Query</th>
				<td><font color="blue">Blue</font></td>
			</tr>
		</tbody>
	</table>

	<hr />

	<table class="datatable">
		<thead>
		<tr>
			<th>
				URI
			</th>
			<th>
				Query + Times
			</th>
			<th>
				Total Queries
			</th>
			<th>
				Distinct Queries
			</th>
			<th>
				Max Time
			</th>
			<th>
				Total Time
			</th>
			<th>
				Optimizable?
			</th>
		</tr>
		</thead>
		<tbody data-link="row" class="rowlink">
			@if(isset($file))
			@foreach($file as $log)
				@if($log['total'] > 0)
				<tr>
					<td style="vertical-align: top;">
						{{$log['uri']}}</font>
					</td>
					<td style="vertical-align: top;">
						<div class="log-dump" dir="ltr">
						<?php 
							$max_time = 0;
							$total_time = 0;
							$unique_queries = 0;
							$optimizable = "no";
							$previous_filled_query = "";
						?>

						@foreach($log['max_times'] as $query => $times)
						<?php $filled_query = $query; ?>
							@foreach($times as $time)
							<?php 
								if(floatval($time['time']) > $max_time) $max_time = (float)$time['time'];
								$total_time += $time['time'];
							?>
							<font color="#f57900"><b>{{$time['time']}}</b></font><br />
							<?php
								foreach ($time['bindings'] as $binding) {
									if($binding instanceof DateTime) {
										$filled_query = preg_replace('/\?/', '<font color="#cc0000">'.Helper::formatDate($binding).'</font>', $filled_query, 1);
									}
									else {
										$filled_query = preg_replace('/\?/', '<font color="#cc0000">'.$binding.'</font>', $filled_query, 1);
									}
								}
							?>
							@if($filled_query == $previous_filled_query)
								<?php $optimizable = "probably"; ?>
								<font color="blue">{{$filled_query}}</font><br />
							@else
								<?php $unique_queries++; ?>
								<font color="black">{{$filled_query}}</font><br />
							@endif
							<?php $previous_filled_query = $filled_query; ?>

							@if(array_key_exists('location', $time))
							file: {{$time['location']['file']}} - line: {{$time['location']['line']}}<br />
							@endif

							@endforeach
						@endforeach
						</div>
					</td>
					<td style="vertical-align: top;">
						{{$log['total']}}
					</td>
					<td style="vertical-align: top;">
						{{$unique_queries}}
					</td>
					<td style="vertical-align: top;">
						{{$max_time}}
					</td>
					<td style="vertical-align: top;">
						{{$total_time/1000}}s
					</td>
					<td style="vertical-align: top;">
						{{$optimizable}}
					</td>
				</tr>
				@endif
			@endforeach
			@endif
		</tbody>
	</table>
<script type="text/javascript">
$('.datatable').dataTable();
</script>
</body>
</html>