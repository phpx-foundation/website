<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RsvpToMeetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_you_can_rsvp_to_a_meetup(): void
    {
        $philly = Group::findByDomain('phpxphilly.com');

        $meetup = Meetup::factory()->for($philly)->create([
            'location' => 'Test Meetup Location',
            'capacity' => 100,
            'starts_at' => now()->addDay()->hour(18)->minute(30),
            'ends_at' => now()->addDay()->hour(20)->minute(0),
        ]);

        $payload = [
            'name' => 'Chris Morrell',
            'email' => 'chris@phpxphilly.com',
            'subscribe' => '1',
            'speaker' => '1',
        ];

        $this->post("https://phpxphilly.com/meetups/{$meetup->id}/rsvps", $payload)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $meetup_user = $meetup->users()->sole();
        $philly_user = $philly->users()->sole();

        $this->assertTrue($meetup_user->is($philly_user));
        $this->assertEquals('Chris Morrell', $meetup_user->name);
        $this->assertEquals('chris@phpxphilly.com', $meetup_user->email);
        $this->assertTrue($philly_user->is_potential_speaker);
        $this->assertTrue($philly_user->group_membership->is_subscribed);
        $this->assertTrue($meetup_user->current_group()->is($philly));

        $user_meetup = $philly_user->meetups()->sole();
        $this->assertTrue($user_meetup->is($meetup));
    }
}
