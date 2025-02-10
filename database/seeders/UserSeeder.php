<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
	public function run(): void
	{
		User::factory()->create([
			'email' => 'admin@phpx.test',
			'password' => bcrypt('password'),
			'email_verified_at' => now(),
		]);
	}
}
