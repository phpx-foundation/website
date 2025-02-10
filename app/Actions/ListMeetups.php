<?php

namespace App\Actions;

use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ListMeetups
{
    use AsAction;

    public static function routes(Router $router): void
    {
        $router->get('api/meetups', static::class);
    }

    /** @return Collection<int, \App\Models\Group|\App\Models\ExternalGroup> */
    public function handle(?Group $group = null): Collection
    {
        return Meetup::query()
            ->withWhereHas('group')
            ->orderByDesc('starts_at')
            ->when($group, fn ($query) => $query->where('group_id', $group->getKey()))
            ->get();
    }

    public function asController(ActionRequest $request)
    {
        $group = Group::findByDomain($request->host());

        $meetups = $this->handle($group);

        return response()->json(['meetups' => $meetups]);
    }
}
