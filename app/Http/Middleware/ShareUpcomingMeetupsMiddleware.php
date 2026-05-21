<?php

namespace App\Http\Middleware;

use App\Actions\GetUpcomingMeetups;
use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

// Note: this is meant to replace the `GetNextMeetup` middleware, but I just
// didn't want to deal with a full refactor before pushing this change.

class ShareUpcomingMeetupsMiddleware
{
	public function handle(Request $request, Closure $next)
	{
		$group = $request->attributes->get('group');
		
		if ($group instanceof Group) {
			View::share('upcoming_meetups', GetUpcomingMeetups::run($group));
		}
		
		return $next($request);
	}
}
