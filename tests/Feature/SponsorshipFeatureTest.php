<?php

namespace Tests\Feature;

use App\Actions\SyncGroups;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SponsorshipFeatureTest extends TestCase
{
	use RefreshDatabase;

	public function test_group_can_have_sponsorship_enabled(): void
	{
		$group = Group::factory()->create([
			'sponsorships_enabled' => true,
			'sponsorship_packages' => [
				[
					'name' => 'Gold Sponsor',
					'currency' => 'USD',
					'amount' => 500,
					'benefits' => ['Logo on materials', 'Speaking slot'],
				],
			],
			'sponsorship_contact_email' => 'sponsors@example.com',
			'sponsorship_description' => 'Support our community',
		]);

		$this->assertTrue($group->acceptsSponsorship());
		$this->assertTrue($group->hasSponsorship());
	}

	public function test_group_without_sponsorship_packages_returns_false_for_has_sponsorship(): void
	{
		$group = Group::factory()->create([
			'sponsorships_enabled' => true,
			'sponsorship_packages' => [],
		]);

		$this->assertTrue($group->acceptsSponsorship());
		$this->assertFalse($group->hasSponsorship());
	}

	public function test_sync_sponsorship_processes_sponsorship_data(): void
	{
		$group = Group::factory()->create([
			'domain' => 'test.domain',
			'name' => 'Test Group',
		]);

		$sponsorshipConfig = [
			'sponsorships' => [
				'enabled' => true,
				'packages' => [
					[
						'name' => 'Silver Sponsor',
						'currency' => 'USD',
						'amount' => 250,
						'benefits' => ['Social media mention'],
					],
				],
				'contact_email' => 'sponsors@test.com',
				'description' => 'Test sponsorship',
			],
		];

		$syncAction = new SyncGroups();
		$reflection = new \ReflectionClass($syncAction);
		$method = $reflection->getMethod('syncSponsorship');
		$method->setAccessible(true);
		
		$method->invoke($syncAction, $group, $sponsorshipConfig);

		$this->assertTrue($group->sponsorships_enabled);
		$this->assertEquals('sponsors@test.com', $group->sponsorship_contact_email);
		$this->assertEquals('Test sponsorship', $group->sponsorship_description);
		$this->assertCount(1, $group->sponsorship_packages);
		$this->assertEquals('Silver Sponsor', $group->sponsorship_packages[0]['name']);
	}

	public function test_sync_groups_handles_missing_sponsorship_data(): void
	{
		$group = Group::factory()->create([
			'domain' => 'test.domain',
			'name' => 'Test Group',
		]);

		// Simulate syncSponsorship with no sponsorship data
		$syncAction = new SyncGroups();
		$reflection = new \ReflectionClass($syncAction);
		$method = $reflection->getMethod('syncSponsorship');
		$method->setAccessible(true);
		
		$method->invoke($syncAction, $group, []); // Empty config

		$this->assertFalse($group->sponsorships_enabled);
		$this->assertNull($group->sponsorship_contact_email);
		$this->assertNull($group->sponsorship_description);
		$this->assertEmpty($group->sponsorship_packages);
	}

	public function test_api_includes_sponsorship_data_for_groups_with_sponsorship(): void
	{
		$group = Group::factory()->create([
			'domain' => 'testdomain.com',
			'sponsorships_enabled' => true,
			'sponsorship_packages' => [
				[
					'name' => 'Gold Sponsor',
					'currency' => 'USD',
					'amount' => 500,
					'benefits' => ['Logo placement', 'Speaking time'],
				],
			],
			'sponsorship_contact_email' => 'sponsors@testdomain.com',
			'sponsorship_description' => 'Support our community',
		]);

		$response = $this->getJson('/api/groups');

		$response->assertStatus(200);
		
		// Verify the group has sponsorship data
		$group->refresh(); // Reload from database
		$this->assertTrue($group->hasSponsorship());
		
		$responseData = $response->json();
		$this->assertTrue($responseData['groups']['testdomain.com']['sponsorship']['enabled']);
		$this->assertEquals('sponsors@testdomain.com', $responseData['groups']['testdomain.com']['sponsorship']['contact_email']);
		$this->assertEquals('Gold Sponsor', $responseData['groups']['testdomain.com']['sponsorship']['packages'][0]['name']);
	}

	public function test_api_excludes_sponsorship_data_for_groups_without_sponsorship(): void
	{
		$group = Group::factory()->create([
			'domain' => 'nosponsor.com',
			'sponsorships_enabled' => false,
		]);

		$response = $this->getJson('/api/groups');

		$response->assertStatus(200);
		$responseData = $response->json();
		$this->assertArrayNotHasKey('sponsorship', $responseData['groups']['nosponsor.com']);
	}
}
