<?php

namespace App\QueryFilter;
use Closure;

class IsDriveFilter{

	public function handle($request, Closure $next){
		$builder = $next($request);

		if( !request()->has('drive')  || empty(request('drive')) ){
			return $builder;
		}

		if(request('drive') == 'yes'){
			return $builder->where('dirve', true);
		}elseif(request('drive') == 'no'){
			return $builder->where('drive', false);
		}
		return $builder;

	}

}
