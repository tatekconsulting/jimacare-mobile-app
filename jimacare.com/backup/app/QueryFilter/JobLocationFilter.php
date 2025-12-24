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

		return $builder->selectRaw('contracts.*, CONVERT( (3959 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(`long`) - radians(?)) + sin(radians(?)) * sin(radians(lat)))), DECIMAL(10, 0)) as miles', [request('lat'), request('long'), request('lat')])
			->join('users', 'contracts.user_id', '=', 'users.id')
			->havingRaw('miles <= ?', [request('radius', 1)])
			->orderByRaw('miles ASC');
	}

}
