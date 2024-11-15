<?php

namespace App\Actions;

use App\Models\Group;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\MailcoachSdk\Mailcoach;
use Throwable;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class ConfigureMailcoach
{
	use AsAction;
	
	public function handle(Group $group, string $token, string $endpoint, string $list, string $email)
	{
		$group->update([
			'email' => $email,
			'mailcoach_token' => $token,
			'mailcoach_endpoint' => $endpoint,
			'mailcoach_list' => $list,
		]);
	}
	
	public function getCommandSignature(): string
	{
		return 'group:configure-mailcoach';
	}
	
	public function asCommand(Command $command): int
	{
		$group = Group::find(select('Which group?', Group::all()->pluck('name', 'id')));
		
		$token = text('What is the mailcoach token?', default: (string) $group->mailcoach_token, required: true);
		$endpoint = text('What is the mailcoach API endpoint?', default: (string) $group->mailcoach_endpoint, required: true);
		$list = text('What is the mailcoach list ID?', default: (string) $group->mailcoach_list, required: true);
		$email = text('What is the group email address?', default: (string) $group->email, required: true);
		
		$client = new Mailcoach($token, $endpoint);
		
		try {
			$client->emailList($list);
		} catch (Throwable $e) {
			$command->error('Unable to connect to MailCoach:');
			$command->error($e->getMessage());
			return 1;
		}
		
		$this->handle($group, $token, $endpoint, $list, $email);
		
		$command->info('MailCoach configured!');
		$command->info('You may want to run `php artisan group:sync-all-mailcoach` next…');
		
		return 0;
	}
}
