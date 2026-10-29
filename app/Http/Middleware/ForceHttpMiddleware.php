<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttpMiddleware
{
	public function handle(Request $request, Closure $next)
	{
		if (config('app.force_https')) {
			$request->server->set('HTTPS', true);
		}

		return $next($request);
	}
}
