<?php

namespace Tests\Feature;

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JoinGroupTest extends TestCase
{
	use RefreshDatabase;
	
	public function test_you_can_access_the_join_page(): void
	{
		$group = Group::factory()->create();
		app()->instance("group:{$group->domain}", $group);
		
		$this->get($group->url('join'))
			->assertOk()
			->assertSee($group->name)
			->assertSee('action="'.$group->url('join').'"', false);
	}
	
	public function test_you_can_join_a_group_and_subscribe_to_updates(): void
	{
		$group = Group::factory()->create();
		app()->instance("group:{$group->domain}", $group);
		$other_group = Group::factory()->create();
		app()->instance("group:{$other_group->domain}", $other_group);
		
		$payload = [
			'name' => 'Chris Morrell',
			'email' => 'chris@phpxphilly.com',
			'subscribe' => '1',
			'speaker' => '0',
		];
		
		// Join Group
		
		$this->post($group->url('join'), $payload)
			->assertSessionHasNoErrors()
			->assertRedirect();
		
		$group_user = $group->users()->sole();
		
		$this->assertEquals('Chris Morrell', $group_user->name);
		$this->assertEquals('chris@phpxphilly.com', $group_user->email);
		$this->assertFalse($group_user->is_potential_speaker);
		$this->assertTrue($group_user->group_membership->is_subscribed);
		$this->assertTrue($group_user->current_group()->is($group));
		
		// Unsubscribe from Group
		
		$this->post($group->url('join'), array_merge($payload, ['subscribe' => '0', 'speaker' => '1']))
			->assertSessionHasNoErrors()
			->assertRedirect();
		
		$group_user = $group->users()->sole();
		
		$this->assertEquals('Chris Morrell', $group_user->name);
		$this->assertEquals('chris@phpxphilly.com', $group_user->email);
		$this->assertTrue($group_user->is_potential_speaker);
		$this->assertFalse($group_user->group_membership->is_subscribed);
		$this->assertTrue($group_user->current_group()->is($group));
		
		// Join Other Group
		
		$this->post($other_group->url('join'), $payload)
			->assertSessionHasNoErrors()
			->assertRedirect();
		
		$other_group_user = $other_group->users()->sole();
		
		$this->assertTrue($other_group_user->is($group_user));
		$this->assertEquals('Chris Morrell', $other_group_user->name);
		$this->assertEquals('chris@phpxphilly.com', $other_group_user->email);
		$this->assertTrue($other_group_user->group_membership->is_subscribed);
		$this->assertTrue($other_group_user->current_group()->is($other_group));
		
		// Unsubscribe from Other Group
		
		$this->post($other_group->url('join'), array_merge($payload, ['subscribe' => '0']))
			->assertSessionHasNoErrors()
			->assertRedirect();
		
		$other_group_user = $other_group->users()->sole();
		
		$this->assertEquals('Chris Morrell', $other_group_user->name);
		$this->assertEquals('chris@phpxphilly.com', $other_group_user->email);
		$this->assertFalse($other_group_user->group_membership->is_subscribed);
		$this->assertTrue($other_group_user->current_group()->is($other_group));
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
		
		$payload = [
			'name' => 'Chris Morrell',
			'email' => 'chris@phpxphilly.com',
			'subscribe' => '1',
			'speaker' => '0',
			'cf-turnstile-response' => '123',
		];
		
		$this->post($group->url('join'), $payload)
			->assertSessionHasErrors('cf-turnstile-response');
		
		$this->post($group->url('join'), $payload)
			->assertSessionHasNoErrors();
	}
}
