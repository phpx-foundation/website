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
			return $factory->newCollection(array_map(fn($raw) => $factory->newFromBuilder($raw), Cache::get($key)))
				->filter(fn(Meetup $meetup) => $meetup->ends_at->isFuture())
				->values();
		}
		
		$meetups = $group->meetups()->future()->orderBy('starts_at')->take(10)->get();
		
		// Expire when the first meetup ends so the list never contains a past meetup
		Cache::put(
			$key,
			$meetups->map(fn(Meetup $meetup) => $meetup->getRawOriginal())->all(),
			$meetups->min('ends_at') ?? now()->addDay()
		);
		
		return $meetups;
	}
}
