<?php

namespace App\QueryFilter;
use Closure;

class ExperienceFilter{

	public function handle($request, Closure $next){
		if( !request()->has('experience')  || empty(request('experience')) ){
			return  $next($request);
		}
		$builder = $next($request);

		return $builder->whereHas('experiences', function($q) {
			return $q->whereIn('id', request('experience') );
		});
	}

}
