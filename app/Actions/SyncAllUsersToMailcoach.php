<?php

namespace App\Actions;

use App\Actions\Concerns\FetchesModelsForCommands;
use App\Models\Group;
use App\Models\User;
use Closure;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class SyncAllUsersToMailcoach
{
	use AsAction;
	use FetchesModelsForCommands;
	
	public function handle(Group $group, ?Closure $callback = null): void
	{
		$callback ??= static fn($user) => null;
		
		$group->users()->eachById(function(User $user) use ($callback, $group) {
			SyncUserToMailcoach::run($group, $user);
			$callback($user);
		});
	}
	
	public function getCommandSignature(): string
	{
		return 'group:sync-all-mailcoach {group?}';
	}
	
	public function asCommand(Command $command): int
	{
		$group = $this->getGroupFromCommand($command);
		
		if (! $group->mailcoach()) {
			$command->error('This group does not have Mailcoach configured.');
			return 1;
		}
		
		$command->line('Syncing all users…');
		$command->newLine();
		
		$this->handle($group, function(User $user) use ($command) {
			$command->line(sprintf(
				" - [%s] %s <%s>",
				$user->group_membership->is_subscribed ? '<info>subscribed</info>' : '<error>unsubscribed</error>',
				$user->name,
				$user->email,
			));
		});
		
		$command->newLine();
		
		return 0;
	}
}
