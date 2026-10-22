<?php

namespace Tests\Feature;

use App\Actions\SyncDomainsWithCloud;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncDomainsWithCloudTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp(): void
	{
		parent::setUp();

		config([
			'services.cloud.url' => 'https://cloud.laravel.com/api',
			'services.cloud.token' => 'test-token',
			'services.cloud.environment' => 'env-123',
		]);
	}

	public function test_it_creates_domains_that_are_missing_from_cloud(): void
	{
		Group::factory()->create(['domain' => 'phpxexisting.com']);
		Group::factory()->create(['domain' => 'phpxpaged.com']);
		Group::factory()->create(['domain' => 'phpxnew.com']);

		Http::fake([
			'cloud.laravel.com/api/environments/env-123/domains?page=2' => Http::response([
				'data' => [$this->domain('phpxpaged.com')],
				'links' => ['next' => null],
			]),
			'cloud.laravel.com/api/environments/env-123/domains' => function(Request $request) {
				if ('POST' === $request->method()) {
					return Http::response(['data' => $this->domain($request['name'])]);
				}

				return Http::response([
					'data' => [$this->domain('phpxexisting.com')],
					'links' => ['next' => 'https://cloud.laravel.com/api/environments/env-123/domains?page=2'],
				]);
			},
		]);

		$created = SyncDomainsWithCloud::run();

		$this->assertSame(['phpxnew.com'], $created->keys()->all());

		Http::assertSentCount(3);
		Http::assertSent(fn(Request $request) => 'POST' === $request->method()
			&& 'Bearer test-token' === $request->header('Authorization')[0]
			&& 'phpxnew.com' === $request['name']
			&& 'dns_proxy' === $request['cloudflare_strategy']
			&& false === $request['allow_downtime']);
	}

	public function test_it_does_nothing_when_all_domains_exist(): void
	{
		Group::factory()->create(['domain' => 'phpxexisting.com']);

		Http::fake([
			'cloud.laravel.com/api/environments/env-123/domains' => Http::response([
				'data' => [$this->domain('phpxexisting.com')],
				'links' => ['next' => null],
			]),
		]);

		$this->assertTrue(SyncDomainsWithCloud::run()->isEmpty());

		Http::assertNotSent(fn(Request $request) => 'POST' === $request->method());
	}

	public function test_it_throws_when_the_api_rejects_the_request(): void
	{
		Group::factory()->create(['domain' => 'phpxnew.com']);

		Http::fake([
			'cloud.laravel.com/api/environments/env-123/domains' => Http::response(['message' => 'Unauthenticated.'], 401),
		]);

		$this->expectException(RequestException::class);

		SyncDomainsWithCloud::run();
	}

	public function test_the_command_lists_domains_and_adds_missing_ones(): void
	{
		Group::factory()->create(['domain' => 'phpxexisting.com']);
		Group::factory()->create(['domain' => 'phpxnew.com']);

		Http::fake([
			'cloud.laravel.com/api/environments/env-123/domains' => function(Request $request) {
				if ('POST' === $request->method()) {
					return Http::response(['data' => $this->domain($request['name'])]);
				}

				return Http::response([
					'data' => [$this->domain('phpxexisting.com')],
					'links' => ['next' => null],
				]);
			},
		]);

		$this->artisan('cloud:sync-domains', ['--force' => true])
			->expectsOutputToContain('Will be added')
			->expectsOutputToContain('Added phpxnew.com')
			->assertExitCode(0);

		Http::assertSent(fn(Request $request) => 'POST' === $request->method() && 'phpxnew.com' === $request['name']);
	}

	public function test_the_command_shows_verification_status_of_existing_domains(): void
	{
		Group::factory()->create(['domain' => 'phpxexisting.com']);
		
		Http::fake([
			'cloud.laravel.com/api/environments/env-123/domains' => Http::response([
				'data' => [$this->domain('phpxexisting.com')],
				'links' => ['next' => null],
			]),
		]);
		
		$this->artisan('cloud:sync-domains')
			->expectsOutputToContain('hostname: verified, ssl: verified, origin: verified')
			->expectsOutputToContain('No changes necessary.')
			->assertExitCode(0);
	}
	
	protected function domain(string $name): array
	{
		return [
			'id' => "dom-{$name}",
			'type' => 'domains',
			'attributes' => [
				'name' => $name,
				'hostname_status' => 'verified',
				'ssl_status' => 'verified',
				'origin_status' => 'verified',
				'dns_records' => [
					'origin_cname' => 'example.laravel.cloud',
				],
			],
		];
	}
}
