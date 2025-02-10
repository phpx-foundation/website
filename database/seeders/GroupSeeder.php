<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $nyc = Group::updateOrCreate([
            'domain' => 'phpxnyc.com',
        ], [
            'name' => 'PHP×NYC',
            'twitter_url' => 'https://twitter.com/joetannenbaum',
            'meetup_url' => 'https://www.meetup.com/php-nyc/',
            'description' => 'A fresh PHP meetup for NYC area devs.',
        ]);

        app()->instance('group:phpxnyc.com', $nyc);

        $philly = Group::updateOrCreate([
            'domain' => 'phpxphilly.com',
        ], [
            'name' => 'PHP×Philly',
            'twitter_url' => 'https://twitter.com/inxilpro',
            'meetup_url' => 'https://www.meetup.com/php-philly/',
            'description' => 'A Philly-area PHP meetup for web artisans who want to learn and connect.',
        ]);

        app()->instance('group:phpxphilly.com', $philly);

        $stl = Group::updateOrCreate([
            'domain' => 'phpxstl.com',
        ], [
            'name' => 'PHP×STL',
            'twitter_url' => 'https://twitter.com/skylerkatz',
            'meetup_url' => 'https://www.meetup.com/php-stl/',
            'description' => 'A St. Louis-area PHP meetup for web artisans who want to learn and connect.',
        ]);

        app()->instance('group:phpxstl.com', $stl);
    }
}
