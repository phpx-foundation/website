<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ForceHttpMiddlewareTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		Route::get('/_test/scheme', fn() => request()->getScheme().' '.asset('build/app.css'));
	}

	public function test_requests_are_treated_as_secure_when_force_https_is_enabled(): void
	{
		config(['app.force_https' => true]);

		$this->get('http://phpx.test/_test/scheme')
			->assertOk()
			->assertSeeText('https https://phpx.test/build/app.css');
	}

	public function test_requests_keep_their_scheme_when_force_https_is_disabled(): void
	{
		config(['app.force_https' => false]);

		$this->get('http://phpx.test/_test/scheme')
			->assertOk()
			->assertSeeText('http http://phpx.test/build/app.css');
	}
}
