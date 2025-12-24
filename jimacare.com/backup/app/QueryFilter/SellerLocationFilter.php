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

		return $builder->selectRaw('*, CONVERT( (3959 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(`long`) - radians(?)) + sin(radians(?)) * sin(radians(lat)))), DECIMAL(10, 0)) as miles', [request('lat'), request('long'), request('lat')])
			->havingRaw('miles <= ?', [request('radius', 5)])
			->orderByRaw('miles ASC')
		;
	}

}
