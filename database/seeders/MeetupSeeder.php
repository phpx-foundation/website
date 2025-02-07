<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Database\Seeder;

class MeetupSeeder extends Seeder
{
	public function run(): void
	{
		Group::eachById(function(Group $group) {
			// One past meetup
			Meetup::factory()->for($group)->create([
				'location' => 'Past Location',
				'starts_at' => now()->subDay()->hour(18)->minute(0),
				'ends_at' => now()->subDay()->hour(21)->minute(0),
			]);
			
			// One future meetup
			Meetup::factory()->for($group)->create([
				'location' => 'Future Location',
				'starts_at' => now()->addWeek()->hour(18)->minute(0),
				'ends_at' => now()->addWeek()->hour(21)->minute(0),
			]);
		});
	}
}
