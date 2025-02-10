<?php

namespace App\Actions;

use App\Models\Meetup;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

use function Laravel\Prompts\select;

class ListRsvps
{
    use AsAction;

    /** @return Collection<int, User> */
    public function handle(Meetup $meetup): Collection
    {
        return $meetup->users;
    }

    public function getCommandSignature(): string
    {
        return 'meetup:list-rsvps {meetup?}';
    }

    public function asCommand(Command $command): int
    {
        if (! $meetup_id = $command->argument('meetup')) {
            $options = Meetup::future()
                ->with('group')
                ->orderBy('starts_at')
                ->limit(50)
                ->get()
                ->mapWithKeys(fn (Meetup $meetup) => [
                    $meetup->getKey() => "{$meetup->group->name} @ {$meetup->location} on {$meetup->range()}",
                ]);

            $meetup_id = select('Which meetup?', $options);
        }

        $users = $this->handle(Meetup::find($meetup_id));

        $headers = ['Name', 'Email', 'Speaker?'];

        $rows = $users->map(fn (User $user) => [
            $user->name,
            $user->email,
            $user->is_potential_speaker ? '✅' : '',
        ]);

        $command->table($headers, $rows);

        return 0;
    }
}
