<?php

namespace App\Actions\Concerns;

use App\Models\Group;
use App\Models\Meetup;
use App\Models\User;
use Illuminate\Console\Command;
use function Laravel\Prompts\select;

trait FetchesModelsForCommands
{
	protected function getGroupFromCommand(Command $command): Group
	{
		$id = $command->argument('group') ?? select(
			label: 'Which group?',
			options: Group::pluck('name', 'id')
		);
		
		return Group::find($id);
	}
	
	protected function getMeetupFromCommand(Command $command, ?Group $group = null, bool $upcoming = false): Meetup
	{
		if (! $id = $command->argument('meetup')) {
			$group ??= Group::find(select('Which group?', Group::pluck('name', 'id')));
			
			$id = select('Which meetup?',
				$group->meetups()
					->when($upcoming, fn($query) => $query->where('starts_at', '>', now()))
					->orderByDesc('starts_at')
					->get()
					->mapWithKeys(fn(Meetup $meetup) => [
						$meetup->getKey() => "{$meetup->location} ({$meetup->date_day})",
					]));
		}
		
		return $group
			? $group->meetups()->firstWhere('id', $id)
			: Meetup::find($id);
	}
	
	protected function getUserFromCommand(Command $command, Group|Meetup|null $parent = null): User
	{
		$id = $command->argument('user') ?? select(
			label: 'Which user?',
			options: $parent
				? $parent->users()->pluck('email', (new User())->qualifyColumn('id'))
				: User::pluck('email', 'id'),
		);
		
		return $parent
			? $parent->users()->firstWhere((new User())->qualifyColumn('id'), $id)
			: User::find($id);
	}
}
