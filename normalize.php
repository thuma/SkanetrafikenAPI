<?php
$all = json_decode(file_get_contents('coord-gtfs.json'));
$allout = array();
foreach($all as $key => $stop)
	{
		$allout[] = $stop;
	}
file_put_contents('coord-gtfs.json',json_encode($allout));
?>
