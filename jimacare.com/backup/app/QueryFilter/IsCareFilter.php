<?php

namespace App\QueryFilter;
use Closure;

class IsCareFilter{

	public function handle($request, Closure $next){
		$builder = $next($request);

		if( !request()->has('care')  || empty(request('care')) ){
			return $builder;
		}

		if(request('care') == 'yes'){
			return $builder->where('care', true);
		}elseif(request('care') == 'no'){
			return $builder->where('care', false);
		}
		return $builder;

	}

}
