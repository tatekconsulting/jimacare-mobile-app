<?php

namespace App\QueryFilter;
use Closure;

class CareUserFilter{

	public function handle($request, Closure $next){
		return $next($request)->where('role', 'care');
	}

}
