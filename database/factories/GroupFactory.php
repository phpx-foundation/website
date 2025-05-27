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
			'domain' => 'phpx'.substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 3).'.test',
			'name' => $this->faker->unique()->name(),
			'timezone' => 'America/New_York',
			'created_at' => now(),
			'updated_at' => now(),
		];
	}
}
