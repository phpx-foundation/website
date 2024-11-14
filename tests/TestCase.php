<?php

namespace Tests;

use Database\Seeders\GroupSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
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
}
