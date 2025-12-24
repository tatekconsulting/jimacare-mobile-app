<?php

namespace App\QueryFilter;
use Closure;

class SkillFilter{

	public function handle($request, Closure $next){
		if( !request()->has('skill')  || empty(request('skill')) ){
			return  $next($request);
		}
		$builder = $next($request);

		return $builder->whereHas('skills', function($q) {
			return $q->whereIn('id', request('skill') );
		});
	}

}
