<?php

namespace App\QueryFilter;
use Closure;

class PostSearchFilter{

	public function handle($request, Closure $next){
		if( !request()->has('search')  || empty(request('search')) ){
			return  $next($request);
		}
		$builder = $next($request);

		return $builder->where(function($q){
			return $q->where('title', 'LIKE', '%' . request('search') . '%')
				->orWhere('desc', 'LIKE', '%' . request('search') . '%')
			;
		});
	}

}
