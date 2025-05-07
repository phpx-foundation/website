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
			$startsAt = now()->tz($group->timezone)->subWeek()->hour(18)->minute(0);
			Meetup::factory()->for($group)->create([
				'location' => 'Past Location',
				'starts_at' => $startsAt,
				'ends_at' => $startsAt->addHours(3),
			]);
			
			// One future meetup
			$startsAt = now()->tz($group->timezone)->addWeek()->hour(18)->minute(0);
			Meetup::factory()->for($group)->create([
				'location' => 'Future Location',
				'starts_at' => $startsAt,
				'ends_at' => $startsAt->addHours(3),
			]);
		});
	}
}
