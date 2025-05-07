<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
	public function run(): void
	{
		$testCity = Group::updateOrCreate([
			'domain' => 'phpxcity.phpx.test',
		], [
			'name' => 'PHP×City',
			'twitter_url' => 'https://twitter.com/phpxcity',
			'bsky_url' => 'https://bsky.app/profile/phpxcity.com',
			'meetup_url' => 'https://www.meetup.com/phpxcity/',
			'description' => 'A PHP meetup for web artisans who want to learn and connect.',
			'timezone' => 'America/Chicago',
		]);
		
		app()->instance('group:phpxcity.phpx.test', $testCity);
	}
}
