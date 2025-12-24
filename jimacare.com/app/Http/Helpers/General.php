<?php

function distanceLatLong($lat1, $lon1, $lat2, $lon2) {
	$dLat = deg2rad($lat2 - $lat1);
	$dLon = deg2rad($lon2 - $lon1);
	$a = (sin($dLat/2) * sin($dLat/2)) .
		(cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
		sin($dLon/2) * sin($dLon/2))
	;
	$a = abs($a);

	$c = 2 * atan2( sqrt($a), sqrt(1-$a) );
	return intval($c * 3958.756);
}

function locationFilter($collection, $request){
	return $collection->filter(function ($data) use ($request){
		return (distanceLatLong($data->lat1, $data->lon1, $request->lat, $request->lat) <= $request->miles);
	});
}
