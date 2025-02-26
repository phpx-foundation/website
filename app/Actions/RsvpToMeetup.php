<?php

namespace App\Actions;

use App\Actions\Concerns\RoutesScopedToGroup;
use App\Actions\Emails\SendRsvpReceipt;
use App\Models\Meetup;
use App\Models\User;
use App\Rules\TurnstileRule;
use Illuminate\Console\Command;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Session;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RsvpToMeetup
{
	use AsAction;
	use RoutesScopedToGroup;
	
	public static function routes(Router $router): void
	{
		$router->post('meetups/{meetup}/rsvps', static::class);
	}
	
	public function handle(Meetup $meetup, User $user): void
	{
		$meetup->users()->syncWithoutDetaching($user->getKey());
		
		SendRsvpReceipt::run($meetup, $user);
	}
	
	public function rules(): array
	{
		return array_merge([
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'email', 'max:255'],
			'subscribe' => ['nullable', 'boolean'],
			'speaker' => ['nullable', 'boolean'],
		], TurnstileRule::rules());
	}
	
	public function asController(ActionRequest $request, Meetup $meetup)
	{
		$remaining = $meetup->remaining();
		
		$user = JoinGroup::run(
			group: $meetup->group,
			name: $request->validated('name'),
			email: $request->validated('email'),
			subscribe: $request->boolean('subscribe'),
			speaker: $request->boolean('speaker'),
		);
		
		$this->handle($meetup, $user);
		
		$message = $remaining > 0
			? 'You are RSVP’d for the meetup!'
			: 'You are waitlisted! We’ll let you know if space opens up.';
		
		Session::flash('message', $message);
		
		return redirect()->back();
	}
	
	public function getCommandSignature(): string
	{
		return 'meetup:rsvp {meetup} {name} {email} {--subscribe} {--speaker}';
	}
	
	public function asCommand(Command $command): int
	{
		$meetup = Meetup::findOrFail($command->argument('meetup'));
		
		$user = JoinGroup::run(
			group: $meetup->group,
			name: $command->argument('name'),
			email: $command->argument('email'),
			subscribe: $command->option('subscribe'),
			speaker: $command->option('speaker'),
		);
		
		$this->handle($meetup, $user);
		
		$command->info('User is now RSVP’d.');
		return 0;
	}
}
