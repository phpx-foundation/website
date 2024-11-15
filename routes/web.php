<?php

use App\Enums\RootDomains;
use App\Http\Middleware\SetGroupFromDomainMiddleware;
use App\Http\Middleware\ShareNextMeetupMiddleware;
use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Support\Facades\Route;

// Register the phpx.world routes
foreach (RootDomains::cases() as $case) {
	Route::domain($case->value)->group(function() {
		Route::get('/', function() {
			return view('world.home', [
				'points' => Group::query()->select(['name', 'latitude', 'longitude'])->get()
					->map(fn($row) => [
						'lat' => $row->latitude, 
						'lng' => $row->longitude,
						'name' => $row->label(),
					])
					->toArray(),
			]);
		});
		Route::view('/organizers', 'world.organizers');
	});
}

// Register the individual group routes
Route::middleware([SetGroupFromDomainMiddleware::class, ShareNextMeetupMiddleware::class])
	->group(function() {
		Route::view('/', 'welcome');
		Route::view('/join', 'join');
		
		Route::get('meetups/{meetup}/rsvps', function(Meetup $meetup) {
			return view('rsvp', ['meetup' => $meetup]);
		});
	});
