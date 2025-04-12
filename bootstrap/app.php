<?php

use App\Http\Middleware\ForceHttpMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\TrustProxies;
use Monicahq\Cloudflare\Http\Middleware\TrustProxies as TrustCloudflareProxies;

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		web: __DIR__.'/../routes/web.php',
		commands: __DIR__.'/../routes/console.php',
		health: '/up',
	)
	->withMiddleware(function(Middleware $middleware) {
		$middleware->replace(TrustProxies::class, TrustCloudflareProxies::class);
		$middleware->prepend(ForceHttpMiddleware::class);
	})
	->withExceptions(function(Exceptions $exceptions) {
	})->create();
