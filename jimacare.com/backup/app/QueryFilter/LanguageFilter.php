<?php

namespace App\QueryFilter;
use Closure;

class LanguageFilter{

	public function handle($request, Closure $next){
		if( !request()->has('language')  || empty(request('language')) ){
			return  $next($request);
		}
		$builder = $next($request);

		return $builder->whereHas('languages', function($q) {
			return $q->whereIn('id', request('language') );
		});
	}

}
