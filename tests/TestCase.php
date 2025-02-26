<?php

namespace Tests;

use Database\Seeders\GroupSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Before;

abstract class TestCase extends BaseTestCase
{
	#[Before]
	public function seedDefaultGroups(): void
	{
		if (method_exists($this, 'refreshDatabase')) {
			$this->afterApplicationCreated(fn() => $this->seed(GroupSeeder::class));
		}
	}
	
	#[Before]
	public function disableVite(): void
	{
		$this->afterApplicationCreated(fn() => $this->withoutVite());
	}
	
	#[Before]
	public function preventStrayRequests(): void
	{
		$this->afterApplicationCreated(function() {
			Http::preventStrayRequests();
			Http::fake(['*cloudflare.com/ips-v4' => Http::response(
				<<<END_CF_IPS
				173.245.48.0/20
				103.21.244.0/22
				103.22.200.0/22
				103.31.4.0/22
				141.101.64.0/18
				108.162.192.0/18
				190.93.240.0/20
				188.114.96.0/20
				197.234.240.0/22
				198.41.128.0/17
				162.158.0.0/15
				104.16.0.0/13
				104.24.0.0/14
				172.64.0.0/13
				131.0.72.0/22
				END_CF_IPS
			)]);
			Http::fake(['*cloudflare.com/ips-v6' => Http::response(
				<<<END_CF_IPS
				2400:cb00::/32
				2606:4700::/32
				2803:f800::/32
				2405:b500::/32
				2405:8100::/32
				2a06:98c0::/29
				2c0f:f248::/32
				END_CF_IPS
			)]);
		});
	}
}
