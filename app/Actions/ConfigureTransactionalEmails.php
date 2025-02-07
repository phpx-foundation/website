<?php

namespace App\Actions;

use App\Actions\Concerns\FetchesModelsForCommands;
use App\Models\Group;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Lody\Lody;
use function Laravel\Prompts\text;

class ConfigureTransactionalEmails
{
	use AsAction;
	use FetchesModelsForCommands;
	
	public function handle(Group $group, Collection $map)
	{
		foreach ($map as $action_name => $mail_name) {
			$group->mailcoach_transactional_emails()->updateOrCreate([
				'group_id' => $group->getKey(),
				'action_name' => $action_name,
			], [
				'mail_name' => $mail_name,
				'is_enabled' => true,
			]);
		}
	}
	
	public function getCommandSignature(): string
	{
		return 'group:configure-transactional-emails {group?}';
	}
	
	public function asCommand(Command $command): int
	{
		$group = $this->getGroupFromCommand($command);
		$existing = $group->mailcoach_transactional_emails()->pluck('mail_name', 'action_name');
		$actions = $this->actions()
			->mapWithKeys(function($fqcn) use ($existing) {
				$mail_name = text(
					label: 'Name for "'.class_basename($fqcn).'"',
					default: $existing->get($fqcn) ?? '',
					hint: 'Leave blank to skip'
				);
				return [$fqcn => $mail_name];
			})
			->filter();
		
		$this->handle($group, collect($actions));
		$command->info('Email mappings saved.');
		
		return 0;
	}
	
	protected function actions()
	{
		return Lody::classes(app_path('Actions/Emails'))
			->isNotAbstract()
			->hasMethod('handle');
	}
}
