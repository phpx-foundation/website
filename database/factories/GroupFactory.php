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
			'domain' => $this->faker->unique()->word().'.test',
			'name' => $this->faker->unique()->name(),
			'created_at' => now(),
			'updated_at' => now(),
		];
	}
}
