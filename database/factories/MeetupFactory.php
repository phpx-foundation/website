<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetupFactory extends Factory
{
    protected $model = Meetup::class;

    public function definition(): array
    {
        static $suffix = 1;

        return [
            'group_id' => Group::factory(),
            'location' => 'Meetup '.$suffix++,
            'description' => 'This is a test meetup. Read all about it: '.$this->faker->words(50, true),
            'capacity' => random_int(20, 50),
            'starts_at' => $starts_at = now()->addDays(random_int(1, 90))->hour(18)->minute(30),
            'ends_at' => $starts_at->clone()->hour(21)->minute(0),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
