<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JoinGroupTest extends TestCase
{
	use RefreshDatabase;
	
	public function test_you_can_access_the_join_page(): void
	{
		$philly = Group::findByDomain('phpxphilly.com');
		$nyc = Group::findByDomain('phpxnyc.com');
		
		$this->get($philly->url('join'))
			->assertOk()
			->assertSee($philly->name)
			->assertSee('action="'.$philly->url('join').'"', false);
		
		$this->get($nyc->url('join'))
			->assertOk()
			->assertSee($nyc->name)
			->assertSee('action="'.$nyc->url('join').'"', false);
	}
	
	public function test_you_can_join_a_group_and_subscribe_to_updates(): void
	{
		$philly = Group::findByDomain('phpxphilly.com');
		$nyc = Group::findByDomain('phpxnyc.com');
		
		$payload = [
			'name' => 'Chris Morrell',
			'email' => 'chris@phpxphilly.com',
			'subscribe' => '1',
			'speaker' => '0',
		];
		
		// Join Philly
		
		$this->post('https://phpxphilly.com/join', $payload)
			->assertSessionHasNoErrors()
			->assertRedirect();
		
		$philly_user = $philly->users()->sole();
		
		$this->assertEquals('Chris Morrell', $philly_user->name);
		$this->assertEquals('chris@phpxphilly.com', $philly_user->email);
		$this->assertFalse($philly_user->is_potential_speaker);
		$this->assertTrue($philly_user->group_membership->is_subscribed);
		$this->assertTrue($philly_user->current_group()->is($philly));
		
		// Unsubscribe from Philly
		
		$this->post('https://phpxphilly.com/join', array_merge($payload, ['subscribe' => '0', 'speaker' => '1']))
			->assertSessionHasNoErrors()
			->assertRedirect();
		
		$philly_user = $philly->users()->sole();
		
		$this->assertEquals('Chris Morrell', $philly_user->name);
		$this->assertEquals('chris@phpxphilly.com', $philly_user->email);
		$this->assertTrue($philly_user->is_potential_speaker);
		$this->assertFalse($philly_user->group_membership->is_subscribed);
		$this->assertTrue($philly_user->current_group()->is($philly));
		
		// Join NYC
		
		$this->post('https://phpxnyc.com/join', $payload)
			->assertSessionHasNoErrors()
			->assertRedirect();
		
		$nyc_user = $nyc->users()->sole();
		
		$this->assertTrue($nyc_user->is($philly_user));
		$this->assertEquals('Chris Morrell', $nyc_user->name);
		$this->assertEquals('chris@phpxphilly.com', $nyc_user->email);
		$this->assertTrue($nyc_user->group_membership->is_subscribed);
		$this->assertTrue($nyc_user->current_group()->is($nyc));
		
		// Unsubscribe from NYC
		
		$this->post('https://phpxnyc.com/join', array_merge($payload, ['subscribe' => '0']))
			->assertSessionHasNoErrors()
			->assertRedirect();
		
		$nyc_user = $nyc->users()->sole();
		
		$this->assertEquals('Chris Morrell', $nyc_user->name);
		$this->assertEquals('chris@phpxphilly.com', $nyc_user->email);
		$this->assertFalse($nyc_user->group_membership->is_subscribed);
		$this->assertTrue($nyc_user->current_group()->is($nyc));
	}
	
	public function test_turnstile_is_required_when_configured(): void
	{
		Http::fakeSequence('*.cloudflare.com/turnstile/*')
			->push(['success' => false])
			->push(['success' => true]);
		
		$philly = Group::findByDomain('phpxphilly.com');
		$philly->update(['turnstile_site_key' => '1', 'turnstile_secret_key' => '2']);
		
		$payload = [
			'name' => 'Chris Morrell',
			'email' => 'chris@phpxphilly.com',
			'subscribe' => '1',
			'speaker' => '0',
			'cf-turnstile-response' => '123',
		];
		
		$this->post('https://phpxphilly.com/join', $payload)
			->assertSessionHasErrors('cf-turnstile-response');
		
		$this->post('https://phpxphilly.com/join', $payload)
			->assertSessionHasNoErrors();
	}
}
