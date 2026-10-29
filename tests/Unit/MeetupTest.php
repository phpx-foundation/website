<?php

namespace Tests\Feature;

use App\Models\Meetup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MeetupTest extends TestCase
{
	use RefreshDatabase;

	#[DataProvider('dateRangeProvider')]
	public function test_it_formats_a_date_range($startsAt, $endsAt, $expected)
	{
		$meetup = Meetup::factory()
			->hasGroup()
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
			['2025-01-01 15:00 UTC', '2025-01-02 16:00 UTC', 'January 1st–January 2nd 2025'],
		];
	}
	
	public function test_it_has_ended_once_its_end_time_is_reached(): void
	{
		$this->freezeTime();
		
		$meetup = Meetup::factory()
			->hasGroup()
			->state([
				'starts_at' => now()->subHours(3),
				'ends_at' => now()->addMinute(),
			])
			->create();
		
		$this->assertFalse($meetup->hasEnded());
		$this->assertTrue(Meetup::query()->future()->exists());
		
		$this->travel(1)->minutes();
		
		$this->assertTrue($meetup->hasEnded());
		$this->assertFalse(Meetup::query()->future()->exists());
	}
	
	public function test_it_can_check_whether_it_has_ended_at_a_given_time(): void
	{
		$meetup = Meetup::factory()
			->hasGroup()
			->state([
				'starts_at' => '2025-01-01 15:00 UTC',
				'ends_at' => '2025-01-01 17:00 UTC',
			])
			->create();
		
		$this->assertFalse($meetup->hasEnded(Date::parse('2025-01-01 16:59 UTC')));
		$this->assertTrue($meetup->hasEnded(Date::parse('2025-01-01 17:00 UTC')));
	}
}
