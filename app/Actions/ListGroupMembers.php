<?php

namespace App\Actions;

use App\Models\Group;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

use function Laravel\Prompts\select;

class ListGroupMembers
{
    use AsAction;

    /** @return Collection<int, User> */
    public function handle(Group $group): Collection
    {
        return $group->users;
    }

    public function getCommandSignature(): string
    {
        return 'group:list-members {group?}';
    }

    public function asCommand(Command $command): int
    {
        $group_id = $command->argument('group') ?? select('Which group?', Group::pluck('name', 'id'));

        $users = $this->handle(Group::find($group_id));

        $headers = ['Name', 'Email', 'Subscribed?', 'Speaker?'];

        $rows = $users->map(fn (User $user) => [
            $user->name,
            $user->email,
            $user->group_membership?->is_subscribed ? '✅' : '',
            $user->is_potential_speaker ? '✅' : '',
        ]);

        $command->table($headers, $rows);

        return 0;
    }
}
