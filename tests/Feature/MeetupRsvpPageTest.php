<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetupRsvpPageTest extends TestCase
{
	use RefreshDatabase;
	
	public function test_an_upcoming_meetup_shows_the_rsvp_form(): void
	{
		$group = Group::factory()->create();
		app()->instance("group:{$group->domain}", $group);
		
		$meetup = Meetup::factory()->for($group)->create([
			'starts_at' => now()->addDay(),
			'ends_at' => now()->addDay()->addHours(2),
		]);
		
		$this->get($group->url("meetups/{$meetup->id}/rsvps"))
			->assertOk()
			->assertSee('action="/meetups/'.$meetup->id.'/rsvps"', false)
			->assertDontSee('This event has ended');
	}
	
	public function test_an_ended_meetup_is_still_viewable_but_offers_group_updates_instead_of_an_rsvp(): void
	{
		$group = Group::factory()->create();
		app()->instance("group:{$group->domain}", $group);
		
		$meetup = Meetup::factory()->for($group)->create([
			'location' => 'Old Location',
			'capacity' => 1,
			'starts_at' => now()->subHours(3),
			'ends_at' => now()->subMinute(),
		]);
		
		$this->get($group->url("meetups/{$meetup->id}/rsvps"))
			->assertOk()
			->assertSee('Meetup @ Old Location')
			->assertSee('This event has ended')
			->assertSee('action="'.$group->url('join').'"', false)
			->assertDontSee('action="/meetups/'.$meetup->id.'/rsvps"', false)
			->assertDontSee('waitlisted');
	}
	
	public function test_an_ended_meetup_does_not_link_to_an_external_rsvp(): void
	{
		$group = Group::factory()->create();
		app()->instance("group:{$group->domain}", $group);
		
		$meetup = Meetup::factory()->for($group)->create([
			'external_rsvp_url' => 'https://example.com/rsvp-for-this-event',
			'starts_at' => now()->subHours(3),
			'ends_at' => now()->subMinute(),
		]);
		
		$this->get($group->url("meetups/{$meetup->id}/rsvps"))
			->assertOk()
			->assertDontSee('example.com/rsvp-for-this-event')
			->assertSee('action="'.$group->url('join').'"', false);
	}
	
	public function test_an_upcoming_meetup_with_an_external_rsvp_links_to_it(): void
	{
		$group = Group::factory()->create();
		app()->instance("group:{$group->domain}", $group);
		
		$meetup = Meetup::factory()->for($group)->create([
			'external_rsvp_url' => 'https://example.com/rsvp-for-this-event',
			'starts_at' => now()->addDay(),
			'ends_at' => now()->addDay()->addHours(2),
		]);
		
		$this->get($group->url("meetups/{$meetup->id}/rsvps"))
			->assertOk()
			->assertSee('example.com/rsvp-for-this-event');
	}
}
