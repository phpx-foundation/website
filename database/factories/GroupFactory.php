<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
	protected $model = Group::class;
	
	public function definition(): array
	{
		return [
			'domain' => $this->faker->word(),
			'name' => $this->faker->name(),
			'created_at' => now(),
			'updated_at' => now(),
		];
	}
}
