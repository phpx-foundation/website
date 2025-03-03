<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MeetupTest extends TestCase
{
	use RefreshDatabase;

	#[DataProvider('dateRangeProvider')]
	public function test_it_formats_a_date_range($startsAt, $endsAt, $expected)
	{
		$meetup = Meetup::factory()
			->state([
				'starts_at' => $startsAt,
				'ends_at' => $endsAt,
			])
			->create();

		$this->assertEquals($expected, $meetup->date_range);
	}

	public static function dateRangeProvider(): array
	{
		return [
			['2025-01-01 15:00 UTC', '2025-01-01 15:00 UTC', 'Wednesday, January 1st 2025 at 10:00am EST'],
			['2025-01-01 15:00 UTC', '2025-01-01 16:00 UTC', 'Wednesday, January 1st 2025 from 10:00am to 11:00am EST'],
			['2025-01-01 15:00 UTC', '2025-01-02 16:00 UTC', 'January 1st – January 2nd 2025'],
		];
	}
}
