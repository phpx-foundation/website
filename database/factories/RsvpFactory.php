<?php

namespace Database\Factories;

use App\Models\Meetup;
use App\Models\Rsvp;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RsvpFactory extends Factory
{
	protected $model = Rsvp::class;
	
	public function definition(): array
	{
		return [
			'meetup_id' => Meetup::factory(),
			'full_name' => $this->faker->name(),
			'email' => $this->faker->unique()->safeEmail(),
			'interests' => $this->faker->word(),
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),
		];
	}
}
