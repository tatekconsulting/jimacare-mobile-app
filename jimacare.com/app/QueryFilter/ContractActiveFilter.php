<?php

namespace App\QueryFilter;

use Closure;

class ContractActiveFilter
{

	public function handle($request, Closure $next)
	{
		return $next($request)->where('contracts.status', 'active');
	}

}
