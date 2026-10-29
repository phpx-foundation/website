<?php

namespace Tests\Feature;

use App\Actions\GetNextMeetup;
use App\Actions\GetUpcomingMeetups;
use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpcomingMeetupsTest extends TestCase
{
    use RefreshDatabase;

    public function test_ended_meetups_are_not_served_from_the_upcoming_cache(): void
    {
        $group = Group::factory()->create();

        Meetup::factory()->for($group)->create([
            'starts_at' => now()->addHours(1),
            'ends_at' => now()->addHours(3),
        ]);
        $later = Meetup::factory()->for($group)->create([
            'starts_at' => now()->addDays(10),
            'ends_at' => now()->addDays(10)->addHours(2),
        ]);

        $this->assertCount(2, GetUpcomingMeetups::run($group));
        $this->assertSame($group->getKey(), GetNextMeetup::run($group)->group_id);

        $this->travel(4)->hours();

        $upcoming = GetUpcomingMeetups::run($group);

        $this->assertCount(1, $upcoming);
        $this->assertTrue($upcoming->sole()->is($later));
        $this->assertTrue(GetNextMeetup::run($group)->is($later));
    }
}
