<?php

namespace App\QueryFilter;
use Closure;

class SellerExperienceFilter{

	public function handle($request, Closure $next){

		$builder = $next($request);

		if( !request()->has('experience')  || empty(request('experience')) ){
			return  $builder;
		}

		// Handle both single value (backward compatibility) and array
		$experienceIds = request('experience');
		if (!is_array($experienceIds)) {
			$experienceIds = [$experienceIds];
		}
		
		// Filter out empty values
		$experienceIds = array_filter($experienceIds, function($id) {
			return !empty($id);
		});

		if (empty($experienceIds)) {
			return $builder;
		}

		return $builder->whereHas('experiences', function($q) use ($experienceIds) {
			return $q->whereIn('id', $experienceIds);
		});
	}

}
