<?php

namespace App\QueryFilter;
use Closure;

class SellerTypeFilter{

	public function handle($request, Closure $next){

		$builder = $next($request);

		if( !request()->has('type')  || empty(request('type')) ){
			return  $builder->where('role_id', '>', 2);
		}

		return $builder->where('role_id', request('type'));
	}

}
