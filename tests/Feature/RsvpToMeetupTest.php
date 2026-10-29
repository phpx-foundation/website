<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RsvpToMeetupTest extends TestCase
{
	use RefreshDatabase;
	
	public function test_you_can_rsvp_to_a_meetup(): void
	{
		$group = Group::factory()->create();
		app()->instance("group:{$group->domain}", $group);
		
		$meetup = Meetup::factory()->for($group)->create([
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
		
		$this->post($group->url("meetups/{$meetup->id}/rsvps"), $payload)
			->assertSessionHasNoErrors()
			->assertRedirect();
		
		$meetup_user = $meetup->users()->sole();
		$group_user = $group->users()->sole();
		
		$this->assertTrue($meetup_user->is($group_user));
		$this->assertEquals('Chris Morrell', $meetup_user->name);
		$this->assertEquals('chris@phpxphilly.com', $meetup_user->email);
		$this->assertTrue($group_user->is_potential_speaker);
		$this->assertTrue($group_user->group_membership->is_subscribed);
		$this->assertTrue($meetup_user->current_group()->is($group));
		
		$user_meetup = $group_user->meetups()->sole();
		$this->assertTrue($user_meetup->is($meetup));
	}
	
	public function test_turnstile_is_required_when_configured(): void
	{
		Http::fakeSequence('*.cloudflare.com/turnstile/*')
			->push(['success' => false])
			->push(['success' => true]);
		
		$group = Group::factory()
			->create([
				'turnstile_site_key' => '1',
				'turnstile_secret_key' => '2',
			]);
		app()->instance("group:{$group->domain}", $group);
		
		$meetup = Meetup::factory()->for($group)->create([
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
			'cf-turnstile-response' => '123',
		];
		
		$this->post($group->url("meetups/{$meetup->id}/rsvps"), $payload)
			->assertSessionHasErrors('cf-turnstile-response');
		
		$this->post($group->url("meetups/{$meetup->id}/rsvps"), $payload)
			->assertSessionHasNoErrors();
	}
	
	public function test_you_cannot_rsvp_to_a_meetup_that_has_ended(): void
	{
		$group = Group::factory()->create();
		app()->instance("group:{$group->domain}", $group);
		
		$meetup = Meetup::factory()->for($group)->create([
			'starts_at' => now()->subHours(3),
			'ends_at' => now()->subMinute(),
		]);
		
		$payload = [
			'name' => 'Chris Morrell',
			'email' => 'chris@phpxphilly.com',
			'subscribe' => '1',
			'speaker' => '1',
		];
		
		$this->post($group->url("meetups/{$meetup->id}/rsvps"), $payload)
			->assertSessionHasErrors('meetup')
			->assertRedirect();
		
		$this->assertDatabaseCount('rsvps', 0);
		$this->assertDatabaseCount('users', 0);
		$this->assertDatabaseCount('group_memberships', 0);
	}
	
	public function test_you_can_rsvp_until_the_moment_a_meetup_ends(): void
	{
		$this->freezeTime();
		
		$group = Group::factory()->create();
		app()->instance("group:{$group->domain}", $group);
		
		$meetup = Meetup::factory()->for($group)->create([
			'starts_at' => now()->subHours(3),
			'ends_at' => now()->addMinute(),
		]);
		
		$payload = [
			'name' => 'Chris Morrell',
			'email' => 'chris@phpxphilly.com',
		];
		
		$this->post($group->url("meetups/{$meetup->id}/rsvps"), $payload)
			->assertSessionHasNoErrors();
		
		$this->assertCount(1, $meetup->users);
		
		$this->travel(1)->minutes();
		
		$this->post($group->url("meetups/{$meetup->id}/rsvps"), array_merge($payload, ['email' => 'other@phpxphilly.com']))
			->assertSessionHasErrors('meetup');
		
		$this->assertCount(1, $meetup->fresh()->users);
	}
	
	public function test_the_command_cannot_rsvp_to_a_meetup_that_has_ended(): void
	{
		$group = Group::factory()->create();
		
		$meetup = Meetup::factory()->for($group)->create([
			'starts_at' => now()->subHours(3),
			'ends_at' => now()->subMinute(),
		]);
		
		$this->artisan('meetup:rsvp', [
			'meetup' => $meetup->id,
			'name' => 'Chris Morrell',
			'email' => 'chris@phpxphilly.com',
		])->assertFailed();
		
		$this->assertDatabaseCount('rsvps', 0);
		$this->assertDatabaseCount('users', 0);
	}
}
