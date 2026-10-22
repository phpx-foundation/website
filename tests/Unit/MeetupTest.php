<?php

namespace Tests\Feature;

use App\Models\Meetup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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
	
	public function test_open_graph_image_is_read_from_the_public_disk(): void
	{
		Storage::fake('public');
		
		$meetup = Meetup::factory()->create();
		
		$this->assertNull($meetup->open_graph_image_file);
		
		Storage::disk('public')->put("og/meetups/{$meetup->getKey()}.png", 'png');
		
		$this->assertSame("og/meetups/{$meetup->getKey()}.png", $meetup->open_graph_image_file);
		$this->assertSame(
			Storage::disk('public')->url("og/meetups/{$meetup->getKey()}.png"),
			$meetup->open_graph_image_url,
		);
	}
}
