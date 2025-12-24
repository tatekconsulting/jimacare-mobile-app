<?php

namespace App\QueryFilter;
use Closure;

class TypeFilter{

	public function handle($request, Closure $next){
		if( !request()->has('type')  || empty(request('type')) ){
			return  $next($request);
		}
		$builder = $next($request);

		return $builder->whereHas('types', function($q) {
			return $q->whereIn('id', request('type') );
		});
	}

}
