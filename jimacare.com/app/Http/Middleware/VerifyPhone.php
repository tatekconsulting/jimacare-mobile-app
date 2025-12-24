<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyPhone
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		if (auth()->id() && !is_null(auth()->user()->phone_verified_at)) {
			return $next($request);
		}
		return redirect('/phone/verify')->with(['type' => 'danger', 'notice' => "Please verify your Phone first."]);
	}
}
