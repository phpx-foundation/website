<?php

namespace App\Http\Middleware;

use App\Models\Group;
use App\Models\Meetup;
use App\Models\Scopes\OrganizedByAuthenticatedUser;
use Closure;
use Illuminate\Http\Request;

class ApplyFilamentScopes
{
    public function handle(Request $request, Closure $next)
    {
        $scope = new OrganizedByAuthenticatedUser;

        Group::addGlobalScope($scope);
        Meetup::addGlobalScope($scope);

        return $next($request);
    }
}
