<?php

use App\Enums\RootDomains;
use App\Http\Controllers\World\HomeController;
use App\Http\Middleware\SetGroupFromDomainMiddleware;
use App\Http\Middleware\ShareNextMeetupMiddleware;
use App\Models\ExternalGroup;
use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Support\Facades\Route;

// Register the phpx.world routes
foreach (RootDomains::cases() as $case) {
	Route::domain($case->value)->group(function() {
		Route::get('/', HomeController::class);
		Route::view('/organizers', 'world.organizers');
		Route::view('/venues', 'world.venues');
		Route::view('/sponsors', 'world.sponsors');
		Route::view('/terms', 'world.terms');
	});
}

// Register the individual group routes
Route::middleware([SetGroupFromDomainMiddleware::class, ShareNextMeetupMiddleware::class])
	->group(function() {
		Route::view('/', 'welcome');
		Route::view('/join', 'join');
		Route::view('/terms', 'world.terms');
		
		Route::get('meetups/{meetup}/rsvps', function(Meetup $meetup) {
			return view('rsvp', ['meetup' => $meetup]);
		});
	});
