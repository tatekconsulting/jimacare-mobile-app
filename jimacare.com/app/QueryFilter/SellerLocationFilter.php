<?php

namespace App\QueryFilter;
use Closure;

class SellerLocationFilter{

	public function handle($request, Closure $next){

		$builder = $next($request)->whereNotNull('lat')->whereNotNull('long');

		if(
			!request()->has('address')  || empty(request('address')) ||
			!request()->has('lat')  || empty(request('lat')) ||
			!request()->has('long')  || empty(request('long'))
		){
			return  $builder;
		}

		// SECURITY: Validate and sanitize inputs to prevent SQL injection
		$lat = (float) request('lat');
		$long = (float) request('long');
		$radius = request()->has('radius') ? (int) request('radius') : null;
		
		// Validate coordinate ranges
		if ($lat < -90 || $lat > 90 || $long < -180 || $long > 180) {
			return $builder; // Invalid coordinates, return without distance filter
		}
		
		// Validate radius - must be provided and within valid range (1-100 miles)
		// If radius is null, 0, or invalid, return empty result (no matches)
		// This ensures we only match service providers when a valid radius is specified
		if (is_null($radius) || $radius < 1 || $radius > 100) {
			// Return query that will match nothing if radius is invalid
			// This ensures only service providers within the client-specified radius receive notifications
			return $builder->whereRaw('1 = 0'); // No matches
		}

		// Calculate distance using Haversine formula and filter by radius
		// Only return service providers within the specified radius (in miles)
		return $builder->selectRaw('*, CONVERT( (3959 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(`long`) - radians(?)) + sin(radians(?)) * sin(radians(lat)))), DECIMAL(10, 2)) as miles', [$lat, $long, $lat])
			->havingRaw('miles <= ?', [$radius])
			->orderByRaw('miles ASC')
		;
	}

}
