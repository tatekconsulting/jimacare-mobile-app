<?php

namespace App\QueryFilter;
use Closure;

class InterestFilter{

	public function handle($request, Closure $next){
		if( !request()->has('interest')  || empty(request('interest')) ){
			return  $next($request);
		}
		$builder = $next($request);

		return $builder->whereHas('interests', function($q) {
			return $q->whereIn('id', request('interest') );
		});
	}

}
