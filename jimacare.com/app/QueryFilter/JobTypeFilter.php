<?php

namespace App\QueryFilter;
use Closure;

class JobTypeFilter{

	public function handle($request, Closure $next){

		$builder = $next($request);

		if (!request()->has('type') || empty(request('type'))) {
			return $builder->where('contracts.role_id', '>', 2);
		}

		return $builder->where('contracts.role_id', request('type'));
	}

}
