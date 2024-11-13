<?php

namespace App\Actions;

use App\Http\Middleware\SetGroupFromDomainMiddleware;
use App\Models\Group;
use App\Models\Meetup;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Date;
use Lorisleiva\Actions\Concerns\AsAction;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;
use function Laravel\Prompts\textarea;

class CreateMeetup
{
	use AsAction;
	
	public static function routes(Router $router): void
	{
		$router->get('/meetups/create', static::class);
		$router->post('/meetups', static::class);
	}
	
	public function handle(
		Group $group,
		string $location,
		string $description,
		int $capacity,
		CarbonInterface $starts_at,
		CarbonInterface $ends_at,
	): Meetup {
		$meetup = $group->meetups()->create([
			'location' => $location,
			'description' => $description,
			'capacity' => $capacity,
			'starts_at' => $starts_at,
			'ends_at' => $ends_at,
		]);
		
		GenerateOpenGraphImage::run($meetup);
		
		return $meetup;
	}
	
	public function getCommandSignature(): string
	{
		return 'meetup:create';
	}
	
	public function asCommand(Command $command): int
	{
		$group = Group::find(select('Which group?', Group::all()->pluck('name', 'id')));
		$location = text('What is the meetup location?', required: true);
		$description = textarea('Event description (markdown)', required: true);
		$capacity = (int) text('What is the capacity of the location?', required: true);
		$starts_at = Date::parse(text('When does the event start?', required: true));
		$ends_at = Date::parse(text('When does the event end?', required: true));
		
		table(['Option', 'Value'], [
			['Group', $group->name],
			['Location', $location],
			['Description', $description],
			['Capacity', $capacity],
			['Starts at', $starts_at->toDateTimeString()],
			['Ends at', $ends_at->toDateTimeString()],
		]);
		
		if (confirm('Is this correct?')) {
			$meetup = $this->handle($group, $location, $description, $capacity, $starts_at, $ends_at);
			$command->info("Created meetup <{$meetup->getKey()}>!");
			return 0;
		}
		
		return 1;
	}
	
	public function getControllerMiddleware(): array
	{
		return ['auth', SetGroupFromDomainMiddleware::class];
	}
	
	public function asController(Request $request)
	{
		if ($request->isMethod('GET')) {
			return view('meetups.create', [
				'groups' => Group::all(),
			]);
		}
		
		$request->validate([
			'group_id' => ['required', 'int', 'exists:groups,id'],
			'description' => ['required', 'string', 'max:50'],
			'location' => ['required', 'string', 'max:120'],
			'capacity' => ['required', 'int', 'min:2'],
			'starts_at' => ['required', 'date_format:Y-m-d\TH:i'],
			'ends_at' => ['required', 'date_format:Y-m-d\TH:i', 'after:starts_at'],
		]);
		
		$meetup = $this->handle(
			group: Group::findOrFail($request->input('group_id')),
			location: $request->input('location'),
			description: $request->input('description'),
			capacity: $request->integer('capacity'),
			starts_at: $request->date('starts_at'),
			ends_at: $request->date('ends_at'), 
		);
		
		return redirect($meetup->rsvp_url);
	}
}
