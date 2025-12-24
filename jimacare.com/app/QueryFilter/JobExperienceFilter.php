<?php

namespace App\QueryFilter;
use Closure;

class JobExperienceFilter{

	public function handle($request, Closure $next){

		$builder = $next($request);

		if( !request()->has('experience')  || empty(request('experience')) ){
			return  $builder;
		}

		return $builder->whereHas('experiences', function($q) {
			return $q->whereIn('id', [request('experience')] );
		});
	}

}
