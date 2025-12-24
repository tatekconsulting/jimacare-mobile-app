<?php

namespace App\QueryFilter;
use Closure;

class ActiveFilter{

	public function handle($request, Closure $next){
		return $next($request)->where('status', 'active');
	}

}
