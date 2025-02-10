<?php

namespace App\Actions;

use App\Actions\Concerns\RoutesScopedToGroup;
use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetNextMeetup
{
    use AsAction;
    use RoutesScopedToGroup;

    public static function routes(Router $router): void
    {
        $router->get('meetup', static::class);
    }

    public function handle(Group $group): ?Meetup
    {
        $key = "group:{$group->getKey()}:next-meetup";

        // If we have a cached meetup, just use that
        if (Cache::has($key)) {
            return (new Meetup)->newFromBuilder(Cache::get($key));
        }

        // If there's not an upcoming meetup, abort
        if (! $meetup = $group->meetups()->future()->orderBy('starts_at')->first()) {
            return null;
        }

        // Otherwise, cache the upcoming meetup and return
        Cache::put($key, $meetup->getRawOriginal(), $meetup->ends_at);

        return $meetup;
    }

    public function asController(ActionRequest $request, Group $group)
    {
        $meetup = $this->handle($group);

        return redirect(url("/meetups/{$meetup->getKey()}/rsvps"));
    }
}
