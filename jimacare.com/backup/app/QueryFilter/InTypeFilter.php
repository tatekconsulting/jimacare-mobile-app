<?php

namespace App\QueryFilter;
use Closure;

class InTypeFilter{

	public function handle($request, Closure $next){
		if( !request()->has('type')  || empty(request('type')) ){
			return  $next($request);
		}
		$builder = $next($request);

		return $builder->whereIn('role_id', request('type') );
	}

}
