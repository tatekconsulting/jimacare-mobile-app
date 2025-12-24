<?php

namespace App\QueryFilter;
use Closure;

class JobLocationFilter{

	public function handle($request, Closure $next){
		$builder = $next($request);//->where('lat', '!=', null)->whereNotNull('long', '!=', null);

		if (
			!request()->has('address') || empty(request('address')) ||
			!request()->has('lat') || empty(request('lat')) ||
			!request()->has('long') || empty(request('long'))
		) {
			return $builder;
		}

		// SECURITY: Validate and sanitize inputs to prevent SQL injection
		$lat = (float) request('lat');
		$long = (float) request('long');
		$radius = (int) request('radius', 1);
		
		// Validate coordinate ranges
		if ($lat < -90 || $lat > 90 || $long < -180 || $long > 180) {
			return $builder; // Invalid coordinates, return without distance filter
		}
		
		// Validate radius (1-100 miles)
		if ($radius < 1 || $radius > 100) {
			$radius = 1; // Default to safe value
		}

		return $builder->selectRaw('contracts.*, CONVERT( (3959 * acos(cos(radians(?)) * cos(radians(contracts.lat)) * cos(radians(contracts.long) - radians(?)) + sin(radians(?)) * sin(radians(contracts.lat)))), DECIMAL(10, 0)) as miles', [$lat, $long, $lat])->join('users', 'contracts.user_id', '=', 'users.id')
			->havingRaw('miles <= ?', [$radius])
			->orderByRaw('miles ASC');
	}

}
