<?php

namespace App\Http\Middleware;

use App\Actions\GetNextMeetup;
use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ShareNextMeetupMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $group = $request->attributes->get('group');

        if ($group instanceof Group) {
            View::share('next_meetup', GetNextMeetup::run($group));
        }

        return $next($request);
    }
}
