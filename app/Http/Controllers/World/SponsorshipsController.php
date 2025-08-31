<?php

namespace App\Http\Controllers\World;

use App\Models\Group;
use Illuminate\Http\Request;

class SponsorshipsController
{
	public function __invoke(Request $request)
	{
		$groupsWithSponsorship = Group::where('sponsorships_enabled', true)
			->whereNotNull('sponsorship_packages')
			->where('sponsorship_packages', '!=', '[]')
			->orderBy('name')
			->get();

		return view('world.sponsorships', [
			'groupsWithSponsorship' => $groupsWithSponsorship,
		]);
	}
}
