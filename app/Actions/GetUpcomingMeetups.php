<?php

namespace App\Actions;

use App\Actions\Concerns\RoutesScopedToGroup;
use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class GetUpcomingMeetups
{
	use AsAction;
	use RoutesScopedToGroup;
	
	/** @return Collection<int, Meetup> */
	public function handle(Group $group): Collection
	{
		$key = "group:{$group->getKey()}:upcoming-meetups";
		
		// If we have cached meetups, just use them
		if (Cache::has($key)) {
			$factory = new Meetup();
			return $factory->newCollection(array_map(fn($raw) => $factory->newFromBuilder($raw), Cache::get($key)));
		}
		
		$meetups = $group->meetups()->future()->orderBy('starts_at')->take(10)->get();
		
		[$raw, $latest_ends_at] = $meetups->reduceSpread(function($raw, $latest_ends_at, Meetup $meetup) {
			$raw[] = $meetup->getRawOriginal();
			$latest_ends_at = max($latest_ends_at, $meetup->ends_at);
			return [$raw, $latest_ends_at];
		}, [], now()->addDay());
		
		Cache::put($key, $raw, $latest_ends_at);
		
		return $meetups;
	}
}
