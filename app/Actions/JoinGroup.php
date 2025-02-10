<?php

namespace App\Actions;

use App\Actions\Concerns\RoutesScopedToGroup;
use App\Models\Group;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class JoinGroup
{
    use AsAction;
    use RoutesScopedToGroup;

    public static function routes(Router $router): void
    {
        $router->post('join', static::class);
    }

    public function handle(Group $group, string $name, string $email, bool $subscribe = false, bool $speaker = false): User
    {
        $user = User::firstOrCreate(
            [
                'email' => $email,
            ], [
                'name' => $name,
                'password' => Hash::make(Str::random(32)),
            ]
        );

        $user->groups()->syncWithoutDetaching([$group->getKey() => ['is_subscribed' => $subscribe]]);

        $user->update([
            'current_group_id' => $group->getKey(),
            'is_potential_speaker' => $speaker,
        ]);

        SyncUserToMailcoach::run($group, $user);

        return $user;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'subscribe' => ['nullable', 'boolean'],
            'speaker' => ['nullable', 'boolean'],
        ];
    }

    public function asController(ActionRequest $request, Group $group)
    {
        $this->handle(
            group: $group,
            name: $request->validated('name'),
            email: $request->validated('email'),
            subscribe: $request->boolean('subscribe'),
            speaker: $request->boolean('speaker'),
        );

        $message = $request->boolean('subscribe')
            ? "You are now subscribed to updates from {$group->name}."
            : "You are now unsubscribed from {$group->name} updates";

        Session::flash('message', $message);

        return redirect()->back();
    }

    public function getCommandSignature(): string
    {
        return 'group:join {group} {name} {email} {--subscribe} {--speaker}';
    }

    public function asCommand(Command $command): int
    {
        $group = Group::query()
            ->when(
                value: is_numeric($command->argument('group')),
                callback: fn (Builder $query) => $query->where('id', $command->argument('group')),
                default: fn (Builder $query) => $query->where('domain', $command->argument('group')),
            )
            ->sole();

        $this->handle(
            group: $group,
            name: $command->argument('name'),
            email: $command->argument('email'),
            subscribe: $command->option('subscribe'),
            speaker: $command->option('speaker'),
        );

        $command->info('User added to group.');

        return 0;
    }
}
